<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tracker;

use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage as Context;

final class TrackMessage implements MessageTracker
{
    use HasTracker;

    public function newContext(string $event): Context
    {
        return new ContextualMessage($event);
    }
}
