<?php

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

    /**
     * @param Message $message
     */
    public function withMessage(Message $message): void;

    /**
     * @param iterable $messageHandlers
     */
    public function withMessageHandlers(iterable $messageHandlers): void;

    /**
     * @param PromiseInterface $promise
     */
    public function withPromise(PromiseInterface $promise): void;

    /**
     * @param bool $isMessageHandled
     */
    public function markMessageHandled(bool $isMessageHandled): void;

    /**
     * @return bool
     */
    public function isMessageHandled(): bool;

    /**
     * @return Generator
     */
    public function messageHandlers(): Generator;

    /**
     * @return Message
     */
    public function message(): Message;

    /**
     * @return null|object|array
     */
    public function transientMessage(): null|object|array;

    /**
     * @return object|array
     */
    public function pullTransientMessage(): object|array;

    /**
     * @return PromiseInterface|null
     */
    public function promise(): ?PromiseInterface;
}
