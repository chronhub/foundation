<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Chronhub\Foundation\Exception\ReportFailed;
use Chronhub\Foundation\Message\Decorator\ChainDecorators;
use Chronhub\Foundation\Message\Producer\SyncMessageProducer;
use Chronhub\Foundation\Reporter\ReportCommand;
use Chronhub\Foundation\Reporter\ReportEvent;
use Chronhub\Foundation\Reporter\ReportQuery;
use Chronhub\Foundation\Reporter\Router\MultipleHandlerRouter;
use Chronhub\Foundation\Reporter\Router\ReporterRouter;
use Chronhub\Foundation\Reporter\Router\SingleHandlerRouter;
use Chronhub\Foundation\Reporter\Subscribers\ChainMessageDecoratorSubscriber;
use Chronhub\Foundation\Reporter\Subscribers\HandleRouter;
use Chronhub\Foundation\Reporter\Subscribers\NameReporterService;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Illuminate\Support\Arr;

final class DefaultReporterManager extends AbstractReporterManager
{
    protected function createReporter(string $type, array $config): Reporter
    {
        $reporter = $this->reporterInstance($type, $config);

        $this->subscribeToReporter($reporter, $type, $config);

        return $reporter;
    }

    protected function reporterInstance(string $type, array $config): Reporter
    {
        $concrete = $config['concrete'] ?? null;

        if (null === $concrete) {
            $concrete = match ($type) {
                'command' => ReportCommand::class,
                'event' => ReportEvent::class,
                'query' => ReportQuery::class,
            };
        }

        if (!is_subclass_of($concrete, Reporter::class)) {
            throw new ReportFailed("Invalid Reporter class name $concrete");
        }

        if (is_string($tracker = $config['tracker_id'] ?? null)) {
            $tracker = $this->container->get($tracker);
        }

        return new $concrete($config['service_id'] ?? $concrete, $tracker);
    }

    protected function subscribeToReporter(Reporter $reporter, string $type, array $config): void
    {
        $subscribers = $this->resolveServices([
            new NameReporterService($reporter->name()),
            $this->fromReporter('messaging.subscribers'),
            $config['messaging']['subscribers'] ?? [],
            $this->chainMessageDecoratorsSubscribers($config),
            $this->reporterRouterSubscriber($type, $config)
        ]);

        $reporter->subscribe(...$subscribers);
    }

    protected function reporterRouterSubscriber(string $type, array $config): MessageSubscriber
    {
        $useContainer = $config['use_container'] ?? true;

        $router = new ReporterRouter(
            $config['map'],
            $this->container->get(MessageAlias::class),
            $useContainer ? $this->container : null,
            $config['handler_method'] ?? null
        );

        $reporterRouter = match ($type) {
            'command', 'query' => new SingleHandlerRouter($router),
            'event' => new MultipleHandlerRouter($router)
        };

        //$messageProducer = $this->createMessageProducer($type, $config['messaging']['producer'] ?? null);
        $messageProducer = new SyncMessageProducer();

        return new HandleRouter($reporterRouter, $messageProducer);
    }

    protected function chainMessageDecoratorsSubscribers(array $config): MessageSubscriber
    {
        $messageDecorators = $this->resolveServices(
            $this->fromReporter('messaging.decorators'),
            $config['messaging']['decorators'] ?? []
        );

        return new ChainMessageDecoratorSubscriber(
            new ChainDecorators(...$messageDecorators)
        );
    }

    protected function resolveServices(array ...$services): array
    {
        return array_map(function ($service) {
            return is_string($service) ? $this->container->make($service) : $service;
        }, Arr::flatten($services));
    }
}
