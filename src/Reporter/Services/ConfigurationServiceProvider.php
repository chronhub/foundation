<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

final class ConfigurationServiceProvider extends ServiceProvider
{
    /**
     * @var Application
     */
    public $app;

    protected string $reporterPath = __DIR__ . '/../../../config/reporter.php';

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([$this->reporterPath => config_path('reporter.php')]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->reporterPath, 'reporter');
    }
}
