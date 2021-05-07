<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tracker;

use Chronhub\Foundation\Support\Contracts\Tracker\Listener;
use Chronhub\Foundation\Support\Contracts\Tracker\TrackerContext;

final class GenericListener implements Listener
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(private string $eventName,
                                callable $callback,
                                private int $priority)
    {
        $this->callback = $callback;
    }

    public function handle(TrackerContext $context): void
    {
        ($this->callback)($context);
    }

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function priority(): int
    {
        return $this->priority;
    }
}
