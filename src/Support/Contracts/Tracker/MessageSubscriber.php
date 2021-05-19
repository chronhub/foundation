<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Tracker;

interface MessageSubscriber extends Subscriber
{
    public function attachToTracker(MessageTracker $tracker): void;
}
