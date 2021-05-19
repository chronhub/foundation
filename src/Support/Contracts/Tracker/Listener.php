<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Tracker;

interface Listener
{
    public function handle(TrackerContext $context): void;

    public function eventName(): string;

    public function priority(): int;
}
