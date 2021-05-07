<?php

namespace Chronhub\Foundation\Support\Contracts\Tracker;

use Throwable;

interface TrackerContext
{
    /**
     * @param string $event
     */
    public function withEvent(string $event): void;

    /**
     * @return string
     */
    public function currentEvent(): string;

    /**
     * @param bool $stopPropagation
     */
    public function stopPropagation(bool $stopPropagation): void;

    /**
     * @return bool
     */
    public function isPropagationStopped(): bool;

    /**
     * @param Throwable $exception
     */
    public function withRaisedException(Throwable $exception): void;

    /**
     * @return Throwable|null
     */
    public function exception(): ?Throwable;

    /**
     * Reset Exception
     *
     * return boolean if exception exists
     *
     * @return bool
     */
    public function resetException(): bool;

    /**
     * @return bool
     */
    public function hasException(): bool;
}
