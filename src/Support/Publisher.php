<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support;

use React\Promise\PromiseInterface;
use Illuminate\Contracts\Container\Container;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;

final class Publisher
{
    private string $reporterName = 'default';
    private bool $raisePromiseException = true;
    private array $messageSubscribers = [];

    public function __construct(private ReporterManager $reporterManager,
                                private Container $container)
    {
    }

    public function command(array|object $command): void
    {
        $reporter = $this->reporterManager->command($this->reporterName);

        $this->handleSubscribers($reporter);

        $reporter->publish($command);
    }

    public function event(array|object $event): void
    {
        $reporter = $this->reporterManager->event($this->reporterName);

        $this->handleSubscribers($reporter);

        $reporter->publish($event);
    }

    public function query(array|object $query): PromiseInterface
    {
        $reporter = $this->reporterManager->query($this->reporterName);

        $this->handleSubscribers($reporter);

        return $reporter->publish($query);
    }

    public function queryHandled(array|object $query): mixed
    {
        $reporter = $this->reporterManager->query($this->reporterName);

        $this->handleSubscribers($reporter);

        return handlePromise($reporter->publish($query), $this->raisePromiseException);
    }

    public function withDriver(string $driver): Publisher
    {
        $this->reporterName = $driver;

        return $this;
    }

    public function withSubscribers(string|MessageSubscriber ...$messageSubscribers): Publisher
    {
        $this->messageSubscribers = $messageSubscribers;

        return $this;
    }

    public function withRaisePromiseException(bool $raisePromiseException): Publisher
    {
        $this->raisePromiseException = $raisePromiseException;

        return $this;
    }

    /**
     * Attach dynamic message subscribers to reporter instance.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function handleSubscribers(Reporter $reporter): void
    {
        foreach ($this->messageSubscribers as &$subscriber) {
            if (is_string($subscriber)) {
                $subscriber = $this->container->make($subscriber);
            }
        }

        $reporter->subscribe(...$this->messageSubscribers);
    }
}
