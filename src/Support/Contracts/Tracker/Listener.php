<?php

namespace Chronhub\Foundation\Support\Contracts\Tracker;

interface Listener
{
    /**
     * @param TrackerContext $context
     */
    public function handle(TrackerContext $context): void;

    /**
     * @return string
     */
    public function eventName(): string;

    /**
     * @return int
     */
    public function priority(): int;
}
