<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tracker;

use Chronhub\Foundation\Support\Contracts\Tracker\TrackerContext;
use Chronhub\Foundation\Support\Contracts\Tracker\Listener;
use Illuminate\Support\Collection;

trait HasTracker
{
    private Collection $listeners;

    public function __construct()
    {
        $this->listeners = new Collection();
    }

    public function listen(string $eventName, callable $eventContext, int $priority = 0): Listener
    {
        $eventSubscriber = new GenericListener($eventName, $eventContext, $priority);

        $this->listeners->push($eventSubscriber);

        return $eventSubscriber;
    }

    public function fire(TrackerContext $context): void
    {
        $this->fireEvent($context, null);
    }

    public function fireUntil(TrackerContext $context, callable $callback): void
    {
        $this->fireEvent($context, $callback);
    }

    public function forget(Listener $eventSubscriber): void
    {
        $this->listeners = $this->listeners->reject(
            fn(Listener $subscriber): bool => $eventSubscriber === $subscriber
        );
    }

    private function fireEvent(TrackerContext $context, ?callable $callback): void
    {
        $currentEvent = $context->currentEvent();

        $this->listeners
            ->filter(fn(Listener $subscriber) => $currentEvent === $subscriber->eventName())
            ->sortByDesc(fn(Listener $subscriber): int => $subscriber->priority(), SORT_NUMERIC)
            ->each(fn(Listener $listener): bool => $this->handleSubscriber($listener, $context, $callback));
    }

    private function handleSubscriber(Listener $listener, TrackerContext $context, ?callable $callback): bool
    {
        $listener->handle($context);

        if ($context->isPropagationStopped()) {
            return false;
        }

        if ($callback && true === $callback($context)) {
            return false;
        }

        return true;
    }
}