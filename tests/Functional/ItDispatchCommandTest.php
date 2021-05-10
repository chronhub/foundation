<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional;

use Chronhub\Foundation\Exception\MessageNotHandled;
use Chronhub\Foundation\Message\Decorator\MarkAsync;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\ReportCommand;
use Chronhub\Foundation\Reporter\Subscribers\CallableMessageSubscriber;
use Chronhub\Foundation\Reporter\Subscribers\HandleCommand;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\Double\SomeCommandHandler;
use Chronhub\Foundation\Tests\OrchestraWithDefaultConfig;
use Chronhub\Foundation\Tests\Spy\ResetExceptionSpySubscriber;
use Generator;
use Illuminate\Contracts\Foundation\Application;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

final class ItDispatchCommandTest extends OrchestraWithDefaultConfig
{
    /**
     * @test
     * @dataProvider provideCommand
     */
    public function it_dispatch_command(array|object $message): void
    {
        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            }
        ]);

        Report::command()->publish($message);

        $this->assertInstanceOf(SomeCommand::class, $pastCommand);

        $headers = $pastCommand->headers();

        $this->assertEquals(ReportCommand::class, $headers[Header::REPORTER_NAME]);
        $this->assertInstanceOf(UuidInterface::class, $headers[Header::EVENT_ID]);
        $this->assertEquals(SomeCommand::class, $headers[Header::EVENT_TYPE]);
        $this->assertInstanceOf(PointInTime::class, $headers[Header::EVENT_TIME]);
    }

    /**
     * @test
     */
    public function it_dispatch_command_as_array(): void
    {
        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            }
        ]);

        $command = [
            'headers' => [Header::EVENT_TYPE => SomeCommand::class],
            'content' => ['name' => 'steph']
        ];

        Report::command()->publish($command);

        $this->assertInstanceOf(SomeCommand::class, $pastCommand);

        $headers = $pastCommand->headers();

        $this->assertEquals(ReportCommand::class, $headers[Header::REPORTER_NAME]);
        $this->assertInstanceOf(UuidInterface::class, $headers[Header::EVENT_ID]);
        $this->assertEquals(SomeCommand::class, $headers[Header::EVENT_TYPE]);
        $this->assertInstanceOf(PointInTime::class, $headers[Header::EVENT_TIME]);
    }

    /**
     * @test
     * @dataProvider provideCommand
     */
    public function it_dispatch_command_asynchronously(array|object $message): void
    {
        $this->app->bind('report.command.default', function (Application $app): ReportCommand {
            return $app[ReporterManager::class]->command();
        });

        $pastCommand = null;

        $defaultConfig = [
            'default' => [
                'service_id' => 'report.command.default',
                'messaging'  => [
                    'decorators'  => [MarkAsync::class],
                    'subscribers' => [HandleCommand::class],
                    'producer'    => 'async',
                ],
                'map'        => [
                    'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                        $pastCommand = $command;
                    }
                ]
            ]];

        $this->app['config']->set('reporter.reporting.command', $defaultConfig);

        $reporter = $this->app['report.command.default'];

        $resetException = new ResetExceptionSpySubscriber(
            Reporter::FINALIZE_EVENT,
            fn(ContextualMessage $context, Throwable $exception): bool => $exception instanceof MessageNotHandled,
            -10000
        );

        $reporter->subscribe($resetException);

        $reporter->publish($message);

        $this->assertTrue($pastCommand->header(Header::ASYNC_MARKER));
    }

    /**
     * @test
     * @dataProvider provideCommand
     */
    public function it_dispatch_command_to_his_named_handler(object|array $message): void
    {
        $handler = new SomeCommandHandler();

        $this->assertFalse($handler->isHandled());

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => $handler
        ]);

        Report::command()->publish($message);

        $this->assertTrue($handler->isHandled());
    }

    /**
     * @test
     * @dataProvider provideCommand
     */
    public function it_dispatch_message_with_predefined_headers(array|object $message): void
    {
        $this->app->bind('report.command.default', function (Application $app): ReportCommand {
            return $app[ReporterManager::class]->command();
        });

        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            }
        ]);

        $headers = [
            Header::REPORTER_NAME => 'report.command.default',
            Header::EVENT_ID      => Uuid::uuid4(),
            Header::EVENT_TYPE    => SomeCommand::class,
            Header::EVENT_TIME    => $this->app[Clock::class]->fromNow(),
        ];

        if (is_array($message)) {
            $message['headers'] = $message['headers'] + $headers;
        } else {
            $message = $message->withHeaders($headers);
        }

        $this->app['report.command.default']->publish($message);

        $this->assertEquals($headers, $pastCommand->headers());
    }

    /**
     * @test
     */
    public function it_subscribe_to_reporter(): void
    {
        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            }
        ]);

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $subscriber = new CallableMessageSubscriber(Reporter::DISPATCH_EVENT,
            function (ContextualMessage $message): void {
                $message->withMessage(
                    new Message(SomeCommand::fromContent(['name' => 'bug']))
                );
            }, Reporter::PRIORITY_INVOKE_HANDLER + 1);

        $reporter = Report::command();
        $reporter->subscribe($subscriber);

        $reporter->publish($message);

        $this->assertEquals('bug', $pastCommand->toContent()['name']);
    }

    public function provideCommand(): Generator
    {
        yield [SomeCommand::fromContent(['name' => 'steph'])];

        yield [new Message(SomeCommand::fromContent(['name' => 'steph']))];

        yield [
            [
                'headers' => [Header::EVENT_TYPE => SomeCommand::class],
                'content' => ['name' => 'steph']
            ]
        ];
    }
}
