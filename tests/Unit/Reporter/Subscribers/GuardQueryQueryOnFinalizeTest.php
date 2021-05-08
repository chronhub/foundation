<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use Chronhub\Foundation\Exception\UnauthorizedException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\Subscribers\GuardQueryOnFinalize;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Reporter\AuthorizeMessage;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Traits\HandlePromise;
use Chronhub\Foundation\Tests\Double\SomeQuery;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Tracker\TrackMessage;
use Prophecy\Prophecy\ObjectProphecy;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

/** @coversDefaultClass \Chronhub\Foundation\Reporter\Subscribers\GuardQueryOnFinalize */
final class GuardQueryQueryOnFinalizeTest extends TestCaseWithProphecy
{
    use HandlePromise;

    private AuthorizeMessage|ObjectProphecy $authorization;
    private ObjectProphecy|MessageAlias $alias;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorization = $this->prophesize(AuthorizeMessage::class);
        $this->alias = $this->prophesize(MessageAlias::class);
    }

    /**
     * @test
     */
    public function it_authorize_message_on_dispatch_event(): void
    {
        $eventType = SomeQuery::class;
        $eventAlias = 'some-query';

        $message = new Message(
            SomeQuery::fromContent(['name' => 'steph']),
            [Header::EVENT_TYPE => $eventType]
        );

        $this->alias->classToAlias($eventType)->willReturn($eventAlias)->shouldBeCalled();
        $this->authorization->isNotGranted($eventAlias, $message, ['name' => 'steph'])->willReturn(false)->shouldBeCalled();

        $tracker = new TrackMessage();

        $subscriber = new GuardQueryOnFinalize($this->authorization->reveal(), $this->alias->reveal());
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::FINALIZE_EVENT);
        $context->withMessage($message);
        $context->withPromise($this->providePromise());

        $tracker->fire($context);

        $promise = $context->promise();

        $this->assertEquals(['name' => 'steph'], $this->handlePromise($promise));
    }

    /**
     * @test
     */
    public function it_raise_exception_when_message_is_not_authorized(): void
    {
        $eventType = SomeQuery::class;
        $alias = 'some-query';

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Unauthorized for event ' . $alias);

        $message = new Message(
            SomeQuery::fromContent(['name' => 'steph']),
            [Header::EVENT_TYPE => $eventType]
        );

        $this->authorization->isNotGranted($alias, $message, ['name' => 'steph'])->willReturn(true)->shouldBeCalled();
        $this->alias->classToAlias($eventType)->willReturn($alias)->shouldBeCalled();

        $tracker = new TrackMessage();

        $subscriber = new GuardQueryOnFinalize($this->authorization->reveal(), $this->alias->reveal());
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::FINALIZE_EVENT);
        $context->withMessage($message);
        $context->withPromise($this->providePromise());

        $tracker->fire($context);

        $this->assertTrue($context->isPropagationStopped());

        $promise = $context->promise();
        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $this->handlePromise($promise, true);
    }

    private function providePromise(): PromiseInterface
    {
        $deferred = new Deferred();

        $deferred->resolve(['name' => 'steph']);

        return $deferred->promise();
    }
}
