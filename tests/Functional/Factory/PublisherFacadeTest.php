<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional\Factory;

use RuntimeException;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\ReportCommand;
use Chronhub\Foundation\Support\Facade\Publish;
use Chronhub\Foundation\Tests\Double\SomeEvent;
use Chronhub\Foundation\Tests\Double\SomeQuery;
use Illuminate\Contracts\Foundation\Application;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\Double\AnotherCommand;
use Chronhub\Foundation\Support\Traits\HandlePromise;
use Chronhub\Foundation\Tests\OrchestraWithDefaultConfig;
use Chronhub\Foundation\Reporter\Subscribers\HandleCommand;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Reporter\Subscribers\CallableMessageSubscriber;

final class PublisherFacadeTest extends OrchestraWithDefaultConfig
{
    use HandlePromise;

    /**
     * @test
     */
    public function it_assert_registered_service(): void
    {
        $this->assertTrue($this->app->bound(Publish::SERVICE_NAME));
    }

    /**
     * @test
     */
    public function it_publish_command_with_default_driver(): void
    {
        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            },
        ]);

        Publish::command(SomeCommand::fromContent(['name' => 'steph']));

        $this->assertInstanceOf(SomeCommand::class, $pastCommand);
    }

    /**
     * @test
     */
    public function it_publish_commands_with_different_instance_of_publisher(): void
    {
        $pastCommand = null;
        $pastAnotherCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            },
            'another-command' => function (AnotherCommand $command) use (&$pastAnotherCommand): void {
                $pastAnotherCommand = $command;
            },
        ]);

        $sub = new CallableMessageSubscriber(
            Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context): void {
                $context->withMessage(
                    $context
                        ->message()
                        ->withHeader('header_command', 'should not be present in past another command')
                );
            }
        );

        Publish::withSubscribers($sub)->command(SomeCommand::fromContent(['name' => 'steph']));

        $this->assertTrue($pastCommand->has('header_command'));

        Publish::command(AnotherCommand::fromContent(['name' => 'steph']));

        $this->assertFalse($pastAnotherCommand->has('header_command'));
    }

    /**
     * @test
     */
    public function it_publish_command_with_another_driver(): void
    {
        $this->app->bind('my_reporter', function (Application $app): ReportCommand {
            return $app[ReporterManager::class]->command('my_reporter');
        });

        $pastCommand = null;

        $config = [
            'my_reporter' => [
                'service_id' => 'my_reporter',
                'messaging'  => [
                    'subscribers' => [HandleCommand::class],
                    'producer'    => 'sync',
                ],
                'map'        => [
                    'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                        $pastCommand = $command;
                    },
                ],
            ],
        ];

        $this->app['config']->set('reporter.reporting.command', $config);

        Publish::withDriver('my_reporter')->command(SomeCommand::fromContent(['name' => 'steph']));

        $this->assertInstanceOf(SomeCommand::class, $pastCommand);
    }

    /**
     * @test
     */
    public function it_publish_event(): void
    {
        $pastEvent = null;

        $this->app['config']->set('reporter.reporting.event.default.map', [
            'some-event' => function (SomeEvent $event) use (&$pastEvent): void {
                $pastEvent = $event;
            },
        ]);

        Publish::event(SomeEvent::fromContent(['name' => 'steph']));

        $this->assertInstanceOf(SomeEvent::class, $pastEvent);
    }

    /**
     * @test
     */
    public function it_publish_query(): void
    {
        $pastQuery = null;

        $this->app['config']->set('reporter.reporting.query.default.map', [
            'some-query' => function (SomeQuery $query, Deferred $promise) use (&$pastQuery): void {
                $pastQuery = $query;
                $promise->resolve($query->toContent());
            },
        ]);

        $result = Publish::query(SomeQuery::fromContent(['name' => 'steph']));

        $this->assertInstanceOf(SomeQuery::class, $pastQuery);
        $this->assertInstanceOf(PromiseInterface::class, $result);
        $this->assertEquals(['name' => 'steph'], $this->handlePromise($result));
    }

    /**
     * @test
     */
    public function it_publish_query_and_handle_promise(): void
    {
        $pastQuery = null;

        $this->app['config']->set('reporter.reporting.query.default.map', [
            'some-query' => function (SomeQuery $query, Deferred $promise) use (&$pastQuery): void {
                $pastQuery = $query;
                $promise->resolve($query->toContent());
            },
        ]);

        $result = Publish::queryHandled(SomeQuery::fromContent(['name' => 'steph']));

        $this->assertInstanceOf(SomeQuery::class, $pastQuery);
        $this->assertNotInstanceOf(PromiseInterface::class, $result);
        $this->assertEquals(['name' => 'steph'], $result);
    }

    /**
     * @test
     */
    public function it_raise_exception_caught_in_promise(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('failed');

        $this->app['config']->set('reporter.reporting.query.default.map', [
            'some-query' => function (): void {
                throw new RuntimeException('failed');
            },
        ]);

        Publish::withRaisePromiseException(true)->queryHandled(SomeQuery::fromContent(['name' => 'steph']));
    }

    /**
     * @test
     */
    public function it_return_exception_caught_in_promise(): void
    {
        $this->app['config']->set('reporter.reporting.query.default.map', [
            'some-query' => function (): void {
                throw new RuntimeException('failed');
            },
        ]);

        $publisher = Publish::withRaisePromiseException(false);

        $exception = $publisher->queryHandled(SomeQuery::fromContent(['name' => 'steph']));

        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertEquals('failed', $exception->getMessage());
    }

    /**
     * @test
     */
    public function it_can_subscribe_to_reporter(): void
    {
        $updateNameContent = new CallableMessageSubscriber(
            Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context): void {
                $context->withMessage(
                    new Message(SomeCommand::fromContent(['name' => 'bug']))
                );
            }, 1);

        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            },
        ]);

        Publish::withSubscribers($updateNameContent)->command(SomeCommand::fromContent(['name' => 'steph']));

        $this->assertEquals('bug', $pastCommand->toContent()['name']);
    }

    /**
     * @test
     */
    public function it_resolve_string_subscriber(): void
    {
        $updateNameContent = new CallableMessageSubscriber(
            Reporter::DISPATCH_EVENT,
            function (ContextualMessage $context): void {
                $context->withMessage(
                    new Message(SomeCommand::fromContent(['name' => 'bug']))
                );
            }, 1);

        $this->app->bind('updateNameContent', fn (): MessageSubscriber => $updateNameContent);

        $pastCommand = null;

        $this->app['config']->set('reporter.reporting.command.default.map', [
            'some-command' => function (SomeCommand $command) use (&$pastCommand): void {
                $pastCommand = $command;
            },
        ]);

        Publish::withSubscribers('updateNameContent')->command(SomeCommand::fromContent(['name' => 'steph']));

        $this->assertEquals('bug', $pastCommand->toContent()['name']);
    }
}
