<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter;

use Chronhub\Foundation\Exception\MessageDispatchFailed;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\HasReporter;
use Chronhub\Foundation\Reporter\Subscribers\CallableMessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCase;

final class HasReporterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $reporter = $this->reporterInstance();

        $this->assertEquals('anonymous_class', $reporter->name());
    }

    /**
     * @test
     */
    public function it_can_subscribe_to_tracker(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $onDispatch = new CallableMessageSubscriber(
            Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context): void {
                $nameContent = $context->message()->event()->toContent()['name'] ?? null;

                $this->assertEquals('steph', $nameContent);

                $context->withMessage(
                    new Message(SomeCommand::fromContent(['name' => 'bug']))
                );

                $context->markMessageHandled(true);
            },
            1
        );

        $onFinalize = new CallableMessageSubscriber(
            Reporter::FINALIZE_EVENT,
            function (ContextualMessage $context): void {
                $nameContent = $context->message()->event()->toContent()['name'] ?? null;

                $this->assertEquals('bug', $nameContent);
            },
            1
        );

        $reporter = $this->reporterInstance();

        $reporter->subscribe($onDispatch, $onFinalize);

        $reporter->publish($message);
    }

    /**
     * @test
     */
    public function it_raise_wrapped_exception_caught_during_dispatching_message()
    {
        $message = new Message(
            SomeCommand::fromContent(['name' => 'steph']),
            [Header::EVENT_TYPE => SomeCommand::class]
        );

        $reporter = $this->reporterInstance();

        $onDispatch = new CallableMessageSubscriber(Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context): void {
                $context->stopPropagation(true);
            }, 1);

        $onFinalize = new CallableMessageSubscriber(Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context): void {
                $this->assertFalse($context->isPropagationStopped());
            }, 10);

        $reporter->subscribe($onDispatch, $onFinalize);

        try {
            $reporter->publish($message);
        } catch (MessageDispatchFailed $exception) {
            $this->assertInstanceOf(MessageDispatchFailed::class, $exception);
        }
    }

    /**
     * @test
     */
    public function it_raise_exception_if_message_is_not_handled(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Message ' . SomeCommand::class . ' was not handled');

        $message = new Message(
            SomeCommand::fromContent(['name' => 'steph']),
            [Header::EVENT_TYPE => SomeCommand::class]
        );

        $reporter = $this->reporterInstance();

        try {
            $reporter->publish($message);
        } catch (MessageDispatchFailed $exception) {
            throw $exception->getPrevious();
        }
    }

    private function reporterInstance(): Reporter
    {
        return new class('anonymous_class') implements Reporter {
            use HasReporter;

            public function publish(object|array $message)
            {
                $context = $this->tracker->newContext(self::DISPATCH_EVENT);

                $context->withMessage($message);

                $this->publishMessage($context);
            }
        };
    }
}
