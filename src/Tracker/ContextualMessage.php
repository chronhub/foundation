<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tracker;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage as Context;
use Generator;
use React\Promise\PromiseInterface;

final class ContextualMessage implements Context
{
    use HasTrackerContext;

    private ?Message $message;
    private object|array|null $transientMessage;
    private iterable $messageHandlers = [];
    private bool $isMessageHandled = false;
    private ?PromiseInterface $promise = null;

    public function withTransientMessage(object|array $transientMessage): void
    {
        $this->transientMessage = $transientMessage;
    }

    public function withMessage(Message $message): void
    {
        $this->message = $message;
    }

    public function withMessageHandlers(iterable $messageHandlers): void
    {
        $this->messageHandlers = $messageHandlers;
    }

    public function withPromise(PromiseInterface $promise): void
    {
        $this->promise = $promise;
    }

    public function markMessageHandled(bool $isMessageHandled): void
    {
        $this->isMessageHandled = $isMessageHandled;
    }

    public function isMessageHandled(): bool
    {
        return $this->isMessageHandled;
    }

    public function messageHandlers(): Generator
    {
        yield from $this->messageHandlers;
    }

    public function transientMessage(): null|object|array
    {
        return $this->transientMessage;
    }

    public function pullTransientMessage(): object|array
    {
        $transientMessage = $this->transientMessage;

        $this->transientMessage = null;

        return $transientMessage;
    }

    public function message(): Message
    {
        return $this->message;
    }

    public function promise(): ?PromiseInterface
    {
        return $this->promise;
    }
}
