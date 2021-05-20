<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tracker\TrackMessage;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;
use Chronhub\Foundation\Reporter\Subscribers\ChainMessageDecoratorSubscriber;

final class ChainMessageDecoratorSubscriberTest extends TestCaseWithProphecy
{
    /**
     * @test
     */
    public function it_chain_message_decorators(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));
        $decoratedMessage = $message->withHeader('some', 'header');

        $tracker = new TrackMessage();

        $decorator = $this->prophesize(MessageDecorator::class);
        $decorator->decorate($message)->willReturn($decoratedMessage)->shouldBeCalled();

        $subscriber = new ChainMessageDecoratorSubscriber($decorator->reveal());

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);

        $subscriber->attachToTracker($tracker);

        $tracker->fire($context);

        $this->assertEquals(['some' => 'header'], $context->message()->headers());
    }
}
