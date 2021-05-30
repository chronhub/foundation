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
    protected string $messagePath = __DIR__ . '/../../../config/message.php';

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([$this->reporterPath => config_path('reporter.php')]);
            $this->publishes([$this->messagePath => config_path('message.php')]);
        }
    }

    public function register(): void
    {
        $packageConfig = array_merge(
            require $this->messagePath,
            require $this->reporterPath,
        );

        $message = $this->app['config']->get('message', []);
        $reporter = $this->app['config']->get('reporter', []);

        $this->app['config']->set('reporter', array_merge($packageConfig, $message, $reporter));
    }
}
