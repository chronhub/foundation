<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Tracker;

interface MessageTracker extends Tracker
{
    public function newContext(string $event): ContextualMessage;
}
