<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional\Factory;

use stdClass;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Exception\ReportFailed;
use Chronhub\Foundation\Reporter\ReportCommand;
use Illuminate\Contracts\Foundation\Application;
use Chronhub\Foundation\Tests\OrchestraWithDefaultConfig;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;

final class ReporterManagerTest extends OrchestraWithDefaultConfig
{
    /**
     * @test
     */
    public function it_return_service_as_singleton(): void
    {
        $reporter = $this->app[ReporterManager::class]->create('default', Messaging::COMMAND);

        $reporter2 = $this->app[ReporterManager::class]->command('default');

        $this->assertEquals($reporter, $reporter2);
    }

    /**
     * @test
     */
    public function it_can_be_extended(): void
    {
        $config = [
            'reporter_command' => [
                'service_id'     => 'reporter_command',
                'concrete'       => ReportCommand::class,
                'tracker_id'     => null,
                'handler_method' => 'command',
                'messaging'      => [
                    'decorators'  => [],
                    'subscribers' => [],
                    'producer'    => 'default',
                ],
                'map'            => [],
            ],
        ];

        $this->app['config']->set('reporter.reporting.command', $config);

        $this->app[ReporterManager::class]->extends(
            'reporter_command',
            'command',
            fn (Application $app, array $config) => new ReportCommand('reporter_command')
        );

        $this->app->bind(
            ReportCommand::class,
            fn (Application $app): Reporter => $app[ReporterManager::class]->command('reporter_command')
        );

        $reporter = Report::command('reporter_command');

        $this->assertEquals('reporter_command', $reporter->name());
    }

    /**
     * @test
     */
    public function it_raise_exception_with_missing_reporter_configuration(): void
    {
        $this->expectException(ReportFailed::class);
        $this->expectExceptionMessage('Invalid reporter configuration with reporter_command driver and command type');

        Report::command('reporter_command');
    }

    /**
     * @test
     */
    public function it_raise_exception_with_invalid_reporter_type(): void
    {
        $this->expectException(ReportFailed::class);
        $this->expectExceptionMessage('Reporter type invalid_type is invalid');

        Report::create('reporter_command', 'invalid_type');
    }

    /**
     * @test
     */
    public function it_raise_exception_with_invalid_reporter_class(): void
    {
        $config = [
            'reporter_command' => [
                'service_id' => 'reporter_command',
                'concrete'   => stdClass::class,
                'map'        => [],
            ],
        ];

        $this->app['config']->set('reporter.reporting.command', $config);

        $this->expectException(ReportFailed::class);
        $this->expectExceptionMessage('Invalid Reporter class name');

        Report::create('reporter_command', 'command');
    }
}
