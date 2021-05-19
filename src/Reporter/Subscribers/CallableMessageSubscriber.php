<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;

final class CallableMessageSubscriber implements MessageSubscriber
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(private string $event, callable $callback, private int $priority = 1)
    {
        $this->callback = $callback;
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen($this->event, function (ContextualMessage $context): void {
            ($this->callback)($context);
        }, $this->priority);
    }
}
