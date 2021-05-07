<?php

namespace Chronhub\Foundation\Support\Contracts\Tracker;

interface MessageTracker extends Tracker
{
    /**
     * @param string $event
     * @return ContextualMessage
     */
    public function newContext(string $event): ContextualMessage;
}
