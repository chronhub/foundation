<?php

namespace Chronhub\Foundation\Support\Contracts\Tracker;

interface MessageSubscriber extends Subscriber
{
    /**
     * @param MessageTracker $tracker
     */
    public function attachToTracker(MessageTracker $tracker): void;
}
