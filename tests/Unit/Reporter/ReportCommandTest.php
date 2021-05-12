<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter;

use Chronhub\Foundation\Exception\MessageDispatchFailed;
use Chronhub\Foundation\Reporter\ReportCommand;
use Chronhub\Foundation\Reporter\Subscribers\CallableMessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\Double\SomeMessage;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tracker\TrackMessage;
use RuntimeException;
use stdClass;
use Throwable;
use function get_class;

final class ReportCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $reporter = $this->reporterInstance(null, null);

        $this->assertEquals(get_class($reporter), $reporter->name());
        $this->assertInstanceOf(MessageTracker::class, $reporter->tracker());
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_reporter_name(): void
    {
        $reporter = $this->reporterInstance('report_command', null);

        $this->assertEquals('report_command', $reporter->name());
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_tracker(): void
    {
        $isCalled = false;

        $tracker = new TrackMessage();
        $tracker->listen(Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context) use (&$isCalled): void {
                $isCalled = true;
                $context->markMessageHandled(true);
            });

        $reporter = $this->reporterInstance(ReportCommand::class, $tracker);

        $this->assertEquals(ReportCommand::class, $reporter->name());
        $this->assertEquals($tracker, $reporter->tracker());

        $reporter->publish(new stdClass());

        $this->assertTrue($isCalled);
    }

    /**
     * @test
     */
    public function it_set_command_as_transient_message_in_context(): void
    {
        $transientMessage = null;

        $subscriber = new CallableMessageSubscriber(
            Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context) use (&$transientMessage): void {
                $transientMessage = $context->transientMessage();
                $context->markMessageHandled(true);
            },
            1
        );

        $reporter = $this->reporterInstance(ReportCommand::class, null);
        $reporter->subscribe($subscriber);
        $reporter->publish(new stdClass);

        $this->assertEquals($transientMessage, new stdClass);
    }

    /**
     * @test
     */
    public function it_queue_commands_while_is_dispatching(): void
    {
        $test = $this;
        $reporter = new class(null, null, $test) extends ReportCommand {
            private int $called = 0;

            public function __construct($reporter, $tracker, private TestCase $test)
            {
                parent::__construct($reporter, $tracker);
            }

            public function publish(object|array $message)
            {
                $this->test->assertEmpty($this->queue);

                $this->queue[] = $message;
                $this->called++;

                if (!$this->isDispatching) {
                    $this->isDispatching = true;

                    try {
                        while ($command = array_shift($this->queue)) {
                            if($this->called === 1){
                                $this->test->assertInstanceOf(SomeCommand::class, $command);
                            }

                            $context = $this->tracker->newContext(self::DISPATCH_EVENT);

                            $context->withTransientMessage($command);

                            $this->publishMessage($context);

                            $this->test->assertEquals(2, $this->called);

                            if($this->queue[0] ?? null){
                                $this->test->assertInstanceOf(SomeMessage::class, $this->queue[0]);
                            }else{
                                $this->test->assertEmpty($this->queue);
                            }
                        }

                        $this->isDispatching = false;
                    } catch (Throwable $exception) {
                        $this->isDispatching = false;

                        throw $exception;
                    }
                }
            }
        };

        $someCommandHandler = new class($reporter) {
            public function __construct(private ReportCommand $reporter) {}

            public function __invoke(SomeCommand $command)
            {
                $this->reporter->publish(new SomeMessage('ok'));
            }
        };

        $subscriber = new CallableMessageSubscriber(
            Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context) use ($reporter, $someCommandHandler): void {
                $message = $context->pullTransientMessage();

                if ($message instanceof SomeCommand) {
                    $someCommandHandler($message);
                }

                $context->markMessageHandled(true);
            },
            1
        );

        $reporter->subscribe($subscriber);

        $reporter->publish(SomeCommand::fromContent(['name' => 'steph']));
    }

    /**
     * @test
     */
    public function it_always_finalize_event(): void
    {
        $reporter = $this->reporterInstance(ReportCommand::class, null);

        $exception = new RuntimeException('foo');
        $isPropagationStopped = true;
        $exceptionSet = false;

        $raiseException = new CallableMessageSubscriber(
            Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context) use ($exception): void {
                $context->stopPropagation(true);

                throw $exception;
            },
            1
        );

        $assertStop = new CallableMessageSubscriber(
            Reporter::FINALIZE_EVENT,
            function (ContextualMessage $context) use (&$isPropagationStopped): void {
                $isPropagationStopped = $context->isPropagationStopped();
            },
            100
        );

        $checkException = new CallableMessageSubscriber(
            Reporter::FINALIZE_EVENT,
            function (ContextualMessage $context) use (&$exceptionSet): void {
                $exceptionSet = $context->hasException();
            },
            1
        );

        try {
            $reporter->subscribe($raiseException, $checkException, $assertStop);
            $reporter->publish(new stdclass());
        } catch (Throwable $e) {
            $this->assertInstanceOf(MessageDispatchFailed::class, $e);
            $this->assertEquals($e->getPrevious(), $exception);
            $this->assertTrue($exceptionSet);
            $this->assertFalse($isPropagationStopped);
        }
    }

    private function reporterInstance(?string $name, ?MessageTracker $tracker): ReportCommand
    {
        return new class($name, $tracker) extends ReportCommand {
            public function tracker(): MessageTracker
            {
                return $this->tracker;
            }
        };
    }
}
