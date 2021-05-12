<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Support\Traits;

use Chronhub\Foundation\Reporter\ReportCommand;
use Chronhub\Foundation\Reporter\ReportEvent;
use Chronhub\Foundation\Reporter\ReportQuery;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;
use Illuminate\Contracts\Foundation\Application;

trait HasServiceReporterRegistered
{
    /**
     * @var Application
     */
    public $app;

    protected function registerDefaultReporter(): void
    {
        $this->registerReportCommand();
        $this->registerReportEvent();
        $this->registerReportQuery();
    }

    protected function registerReportCommand(): void
    {
        $this->app->bind(ReportCommand::class, function (Application $app): ReportCommand {
            return $app[ReporterManager::class]->command();
        });
    }

    protected function registerReportEvent(): void
    {
        $this->app->bind(ReportEvent::class, function (Application $app): ReportEvent {
            return $app[ReporterManager::class]->event();
        });
    }

    protected function registerReportQuery(): void
    {
        $this->app->bind(ReportQuery::class, function (Application $app): ReportQuery {
            return $app[ReporterManager::class]->query();
        });
    }
}
