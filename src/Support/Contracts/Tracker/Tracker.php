<?php

namespace Chronhub\Foundation\Support\Contracts\Tracker;

interface Tracker
{
    /**
     * @param string $eventName
     * @param callable $eventContext
     * @param int $priority
     * @return Listener
     */
    public function listen(string $eventName, callable $eventContext, int $priority = 0): Listener;

    /**
     * @param TrackerContext $context
     */
    public function fire(TrackerContext $context): void;

    /**
     * @param ContextualMessage $contextEvent
     * @param callable $callback
     */
    public function fireUntil(ContextualMessage $contextEvent, callable $callback): void;

    /**
     * @param Listener $eventSubscriber
     */
    public function forget(Listener $eventSubscriber): void;
}
