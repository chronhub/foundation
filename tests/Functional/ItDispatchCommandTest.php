<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\ReportCommand;
use Chronhub\Foundation\Reporter\Subscribers\CallableMessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\Double\SomeCommandHandler;
use Chronhub\Foundation\Tests\TestCaseWithOrchestra;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class ItDispatchCommandTest extends TestCaseWithOrchestra
{
    /**
     * @test
     */
    public function it_dispatch_command(): void
    {
        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            }
        ]);

        $command = SomeCommand::fromContent(['name' => 'steph']);

        Report::command()->publish($command);

        $this->assertInstanceOf(SomeCommand::class, $pastCommand);

        $headers = $pastCommand->headers();

        $this->assertEquals(ReportCommand::class, $headers[Header::BUS_NAME]);
        $this->assertInstanceOf(UuidInterface::class, $headers[Header::EVENT_ID]);
        $this->assertInstanceOf(PointInTime::class, $headers[Header::EVENT_TIME]);
    }

    /**
     * @test
     */
    public function it_dispatch_command_to_his_named_handler(): void
    {
        $command = SomeCommand::fromContent(['name' => 'steph']);

        $handler = new SomeCommandHandler();

        $this->assertFalse($handler->isHandled());

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => $handler
        ]);

        Report::command()->publish($command);

        $this->assertTrue($handler->isHandled());
    }

    /**
     * @test
     */
    public function it_dispatch_message_with_predefined_headers(): void
    {
        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            }
        ]);

        $headers = [
            Header::BUS_NAME   => 'reporter.service_id',
            Header::EVENT_ID   => Uuid::uuid4(),
            Header::EVENT_TIME => $this->app[Clock::class]->fromNow(),
        ];

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), $headers);

        Report::command()->publish($message);

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
}
