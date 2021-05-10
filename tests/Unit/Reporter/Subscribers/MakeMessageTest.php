<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\Subscribers\MakeMessage;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Tracker\TrackMessage;
use Generator;
use Prophecy\Prophecy\ObjectProphecy;

final class MakeMessageTest extends TestCaseWithProphecy
{
    private ObjectProphecy|MessageFactory $factory;
    private TrackMessage $tracker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->prophesize(MessageFactory::class);
        $this->tracker = new TrackMessage();
    }

    /**
     * @test
     * @dataProvider provideEvent
     */
    public function it_create_message_from_context_transient_message(array|object $event): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $this->factory->createFromMessage($event)->willReturn($message)->shouldBeCalled();

        $context = $this->tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withTransientMessage($event);

        $subscriber = new MakeMessage($this->factory->reveal());
        $subscriber->attachToTracker($this->tracker);

        $this->tracker->fire($context);

        $this->assertEquals($message, $context->message());
    }

    public function provideEvent(): Generator
    {
        yield [
            'headers' => [Header::EVENT_TYPE => SomeCommand::class],
            'content' => ['name' => 'steph']
        ];

        $event = SomeCommand::fromContent(['name' => 'steph']);

        yield [$event];

        yield [new Message($event)];
    }
}
