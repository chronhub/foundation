<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Illuminate\Support\ServiceProvider;
use Chronhub\Foundation\Support\Publisher;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Support\Facade\Publish;
use Illuminate\Contracts\Foundation\Application;
use Chronhub\Foundation\Support\Facade\AliasMessage;
use Illuminate\Contracts\Support\DeferrableProvider;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;

class ReporterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @var Application
     */
    public $app;

    public function register(): void
    {
        $this->app->singleton(ReporterManager::class, DefaultReporterManager::class);
        $this->app->alias(ReporterManager::class, Report::SERVICE_NAME);

        $this->app->bind(Publish::SERVICE_NAME, Publisher::class);

        $config = config('reporter');

        $this->app->bind(Clock::class, $config['clock']);

        $message = $config['messaging'];

        $this->app->bind(MessageFactory::class, $message['factory']);
        $this->app->bind(MessageSerializer::class, $message['serializer']);

        $this->app->bind(MessageAlias::class, $message['alias']);
        $this->app->alias(MessageAlias::class, AliasMessage::SERVICE_NAME);
    }

    public function provides(): array
    {
        return [
            DefaultReporterManager::class,
            Report::SERVICE_NAME,
            MessageFactory::class,
            MessageSerializer::class,
            MessageAlias::class,
            AliasMessage::SERVICE_NAME,
            Publish::SERVICE_NAME,
        ];
    }
}
