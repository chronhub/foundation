<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use stdClass;
use Prophecy\Prophecy\ObjectProphecy;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tracker\TrackMessage;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Reporter\Subscribers\HandleRouter;
use Chronhub\Foundation\Support\Contracts\Reporter\Router;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Message\MessageProducer;

final class HandleRouterTest extends TestCaseWithProphecy
{
    private ObjectProphecy|Router $router;
    private MessageProducer|ObjectProphecy $producer;
    private TrackMessage $tracker;
    private Message $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->prophesize(Router::class);
        $this->producer = $this->prophesize(MessageProducer::class);
        $this->tracker = new TrackMessage();
        $this->message = new Message(new stdClass());
    }

    /**
     * @test
     */
    public function it_handle_message_sync(): void
    {
        $this->producer->isSync($this->message)->willReturn(true)->shouldBeCalled();
        $this->router->route($this->message)->willReturn([function (): void {}])->shouldBeCalled();

        $context = $this->tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($this->message);

        $subscriber = new HandleRouter($this->router->reveal(), $this->producer->reveal());
        $subscriber->attachToTracker($this->tracker);

        $this->tracker->fire($context);

        $this->assertEquals($this->message, $context->message());
        $this->assertEquals([function (): void {}], iterator_to_array($context->messageHandlers()));
    }

    /**
     * @test
     */
    public function it_handle_message_async(): void
    {
        $asyncMarkedMessage = new Message(
            SomeCommand::fromContent(['name' => 'steph']),
            [Header::ASYNC_MARKER => true]
        );

        $this->producer->isSync($this->message)->willReturn(false)->shouldBeCalled();
        $this->producer->produce($this->message)->willReturn($asyncMarkedMessage)->shouldBeCalled();
        $this->router->route($this->message)->shouldNotBeCalled();

        $context = $this->tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($this->message);

        $subscriber = new HandleRouter($this->router->reveal(), $this->producer->reveal());
        $subscriber->attachToTracker($this->tracker);

        $this->tracker->fire($context);

        $this->assertEquals($asyncMarkedMessage, $context->message());
        $this->assertEmpty(iterator_to_array($context->messageHandlers()));
    }
}
