<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Spy;

use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;

final class ResetExceptionSpySubscriber implements MessageSubscriber
{
    /**
     * @var callable
     */
    private $condition;

    public function __construct(private string $event,
                                callable $condition,
                                private int $priority = 1)
    {
        $this->condition = $condition;
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen($this->event, function (ContextualMessage $context): void {
            if ($context->hasException() && true === ($this->condition)($context, $context->exception()->getPrevious())) {
                $context->resetException();
            }
        }, $this->priority);
    }
}
