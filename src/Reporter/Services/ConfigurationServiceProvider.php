<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

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
            $this->publishes([
                $this->reporterPath => config_path('reporter.php'),
                $this->messagePath  => config_path('message.php'),
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->messagePath, 'reporter');
        $this->mergeConfigFrom($this->reporterPath, 'reporter');
    }
}
