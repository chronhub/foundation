<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use RuntimeException;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tracker\TrackMessage;
use Chronhub\Foundation\Tests\Double\SomeEvent;
use Chronhub\Foundation\Exception\CollectedExceptionMessage;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Reporter\Subscribers\HandleSequentialEvent;

final class HandleSequentialEventTest extends TestCase
{
    /**
     * @test
     */
    public function it_handle_event_handlers(): void
    {
        $message = new Message(SomeEvent::fromContent(['name' => 'steph']));

        $handled = [false, false];
        $oneHandler = function () use (&$handled): void {
            $handled[0] = true;
        };

        $secondHandler = function () use (&$handled): void {
            $handled[1] = true;
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleSequentialEvent(true);
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$oneHandler, $secondHandler]);

        $tracker->fire($context);

        $this->assertEquals([true, true], $handled);
        $this->assertTrue($context->isMessageHandled());
    }

    /**
     * @test
     */
    public function it_raise_wrapped_collected_exceptions(): void
    {
        $message = new Message(SomeEvent::fromContent(['name' => 'steph']));

        $oneHandler = function (): void {
            throw new RuntimeException('some_message');
        };

        $secondHandler = function (): void {
            throw new RuntimeException('another_message');
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleSequentialEvent(true);
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$oneHandler, $secondHandler]);

        $exception = null;

        try {
            $tracker->fire($context);
        } catch (CollectedExceptionMessage $exception) {
        }

        $this->assertFalse($context->isMessageHandled());
        $this->assertInstanceOf(CollectedExceptionMessage::class, $exception);

        $this->assertEquals('some_message', $exception->getExceptions()[0]->getMessage());
        $this->assertEquals('another_message', $exception->getExceptions()[1]->getMessage());
    }

    /**
     * @test
     */
    public function it_set_wrapped_collected_exceptions_on_context(): void
    {
        $message = new Message(SomeEvent::fromContent(['name' => 'steph']));

        $oneHandler = function (): void {
            throw new RuntimeException('some_message');
        };

        $secondHandler = function (): void {
            throw new RuntimeException('another_message');
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleSequentialEvent(false);
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$oneHandler, $secondHandler]);

        $tracker->fire($context);

        $exception = $context->exception();

        $this->assertFalse($context->isMessageHandled());
        $this->assertInstanceOf(CollectedExceptionMessage::class, $exception);

        $exceptions = $exception->getExceptions();

        $this->assertCount(2, $exceptions);
        $this->assertEquals('some_message', $exceptions[0]->getMessage());
        $this->assertEquals('another_message', $exceptions[1]->getMessage());
    }

    /**
     * @test
     */
    public function it_mark_message_handled_of_one_handler_succeeds(): void
    {
        $message = new Message(SomeEvent::fromContent(['name' => 'steph']));

        $handled = false;
        $oneHandler = function () use (&$handled): void {
            $handled = true;
        };

        $secondHandler = function (): void {
            throw new RuntimeException('another_message');
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleSequentialEvent(false);
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$oneHandler, $secondHandler]);

        $tracker->fire($context);

        $this->assertTrue($handled);
        $this->assertTrue($context->isMessageHandled());

        $exception = $context->exception();
        $this->assertInstanceOf(CollectedExceptionMessage::class, $exception);

        $exceptions = $exception->getExceptions();

        $this->assertCount(1, $exceptions);
        $this->assertEquals('another_message', $exceptions[0]->getMessage());
    }
}
