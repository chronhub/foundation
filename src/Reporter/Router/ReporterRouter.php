<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Router;

use Chronhub\Foundation\Exception\ReportFailed;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Reporter\Router;
use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

final class ReporterRouter implements Router
{
    public function __construct(private array $map,
                                private MessageAlias $messageAlias,
                                private ?Container $container,
                                private ?string $callableMethod)
    {
    }

    public function route(Message $message): iterable
    {
        return $this
            ->determineMessageHandler($message)
            ->transform(
                fn($messageHandler): callable => $this->messageHandlerToCallable($messageHandler)
            );
    }

    private function messageHandlerToCallable(callable|object|string $messageHandler): callable
    {
        if (is_string($messageHandler)) {
            $messageHandler = $this->locateStringMessageHandler($messageHandler);
        }

        if (is_callable($messageHandler)) {
            return $messageHandler;
        }

        if ($this->callableMethod && method_exists($messageHandler, $this->callableMethod)) {
            return Closure::fromCallable([$messageHandler, $this->callableMethod]);
        }

        throw ReportFailed::messageHandlerNotSupported();
    }

    /**
     * @param Message $message
     * @return Collection
     */
    private function determineMessageHandler(Message $message): Collection
    {
        $messageAlias = $this->messageAlias->instanceToAlias($message->event());

        if (null === $messageHandlers = $this->map[$messageAlias] ?? null) {
            throw ReportFailed::messageNameNotFound($messageAlias);
        }

        $messageHandlers = is_array($messageHandlers) ? $messageHandlers : [$messageHandlers];

        return new Collection($messageHandlers);
    }

    private function locateStringMessageHandler(string $messageHandler): object
    {
        if (!$this->container) {
            throw ReportFailed::missingContainer($messageHandler);
        }

        return $this->container->make($messageHandler);
    }
}
