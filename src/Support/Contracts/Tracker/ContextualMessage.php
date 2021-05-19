<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Tracker;

use Chronhub\Foundation\Message\Message;
use Generator;
use React\Promise\PromiseInterface;

interface ContextualMessage extends TrackerContext
{
    /**
     * @param object|array $transientMessage
     */
    public function withTransientMessage(object|array $transientMessage): void;

    public function withMessage(Message $message): void;

    public function withMessageHandlers(iterable $messageHandlers): void;

    public function withPromise(PromiseInterface $promise): void;

    public function markMessageHandled(bool $isMessageHandled): void;

    public function isMessageHandled(): bool;

    public function messageHandlers(): Generator;

    public function message(): Message;

    /**
     * @return object|array|null
     */
    public function transientMessage(): null|object|array;

    /**
     * @return object|array
     */
    public function pullTransientMessage(): object|array;

    public function promise(): ?PromiseInterface;
}
