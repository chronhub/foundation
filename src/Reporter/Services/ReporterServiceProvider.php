<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Chronhub\Foundation\Clock\UniversalSystemClock;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;
use Chronhub\Foundation\Support\Facade\AliasMessage;
use Chronhub\Foundation\Support\Facade\Publish;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Support\Publisher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

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

        $this->app->bind(Clock::class, UniversalSystemClock::class);

        $message = $config['messaging'];

        $this->app->bindIf(MessageFactory::class, $message['factory']);
        $this->app->bindIf(MessageSerializer::class, $message['serializer']);

        $this->app->bindIf(MessageAlias::class, $message['alias']);
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
