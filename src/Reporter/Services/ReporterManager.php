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
use Chronhub\Foundation\Support\Contracts\Message\Messaging;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

final class ReporterManager
{
    /**
     * @var array<string,Reporter>
     */
    protected array $reporters = [];

    /**
     * @var array<string,callable>
     */
    protected array $customerReporters = [];

    protected array $config;

    public function __construct(private Container $container)
    {
        $this->config = $container->get(Repository::class)->get('reporter');
    }

    public function create(string $driver, string $type): Reporter
    {
        $reporterKey = $this->determineReporterKey($driver, $type);

        if ($customerReporter = ($this->customerReporters[$reporterKey] ?? null)) {
            return $customerReporter($this->container, $this->config);
        }

        $config = $this->fromReporter("reporting.$type.$driver");

        if (!is_array($config) || empty($config)) {
            throw new ReportFailed("Invalid reporter configuration with $driver driver and $type type");
        }

        return $this->reporters[$reporterKey] = $this->createReporter($type, $config);
    }

    public function command(string $driver = 'default'): Reporter
    {
        return $this->create($driver ?? 'default', Messaging::COMMAND);
    }

    public function event(string $driver = 'default'): Reporter
    {
        return $this->create($driver ?? 'default', Messaging::EVENT);
    }

    public function query(string $driver = 'default'): Reporter
    {
        return $this->create($driver ?? 'default', Messaging::QUERY);
    }

    public function extends(string $driver, string $type, callable $reporter): void
    {
        $reporterKey = $this->determineReporterKey($driver, $type);

        $this->customerReporters[$reporterKey] = $reporter;
    }

    protected function createReporter(string $type, array $config): Reporter
    {
        $reporter = $this->reporterInstance($type, $config);

        $this->subscribeToReporter($reporter, $type, $config);

        return $reporter;
    }

    protected function reporterInstance(string $type, array $config): Reporter
    {
        [$concrete, $alias] = $this->determineReporterService($type, $config);

        if (is_string($tracker = $config['tracker_id'] ?? null)) {
            $tracker = $this->container->get($tracker);
        }

        return new $concrete($alias, $tracker);
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

    protected function determineReporterService(string $type, array $config): array
    {
        $reporterClass = $config['concrete'] ?? null;

        if (null === $reporterClass) {
            $reporterClass = match ($type) {
                'command' => ReportCommand::class,
                'event' => ReportEvent::class,
                'query' => ReportQuery::class,
                default => throw new ReportFailed("Invalid Reporter class name $reporterClass with type $type")
            };
        }

        return [$reporterClass, $config['service_id'] ?? $reporterClass];
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
            'event' => new MultipleHandlerRouter($router),
            default => throw new ReportFailed("Unable to configure reporter router for type $type"),
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

    protected function determineReporterKey(string $driver, string $type): string
    {
        if (!in_array($type, Messaging::TYPES, true)) {
            throw new ReportFailed("Reporter type $type does not exists");
        }

        return $type . ':' . $driver;
    }

    protected function fromReporter(string $key, $default = null): mixed
    {
        return Arr::get($this->config, $key, $default);
    }
}
