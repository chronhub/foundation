<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Chronhub\Foundation\Clock\UniversalSystemClock;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Chronhub\Foundation\Support\Facade\AliasMessage;
use Chronhub\Foundation\Support\Facade\Report;
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
        $this->app->singleton(ReporterManager::class);
        $this->app->alias(ReporterManager::class, Report::SERVICE_NAME);

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
            ReporterManager::class,
            Report::SERVICE_NAME,
            MessageFactory::class,
            MessageSerializer::class,
            MessageAlias::class,
            AliasMessage::SERVICE_NAME
        ];
    }
}
