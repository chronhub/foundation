<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Tracker;

use Throwable;

interface TrackerContext
{
    public function withEvent(string $event): void;

    public function currentEvent(): string;

    public function stopPropagation(bool $stopPropagation): void;

    public function isPropagationStopped(): bool;

    public function withRaisedException(Throwable $exception): void;

    public function exception(): ?Throwable;

    /**
     * Reset Exception.
     *
     * return boolean if exception exists
     */
    public function resetException(): bool;

    public function hasException(): bool;
}
