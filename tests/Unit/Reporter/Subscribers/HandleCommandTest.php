<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\Subscribers\HandleCommand;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tracker\TrackMessage;

final class HandleCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_handle_command(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $handled = false;
        $messageHandler = function () use (&$handled): void {
            $handled = true;
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleCommand();
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$messageHandler]);

        $tracker->fire($context);

        $this->assertTrue($handled);
        $this->assertTrue($context->isMessageHandled());
    }

    /**
     * @test
     */
    public function it_does_mark_message_handled_when_context_message_handlers_is_empty(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $tracker = new TrackMessage();

        $subscriber = new HandleCommand();
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);

        $tracker->fire($context);

        $this->assertFalse($context->isMessageHandled());
    }
}
