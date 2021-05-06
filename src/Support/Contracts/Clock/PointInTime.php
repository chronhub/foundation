<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Clock;

use DateInterval;
use DateTimeImmutable;

interface PointInTime
{
    /**
     * @param string $pointInTime
     * @return self
     */
    public static function fromString(string $pointInTime): self;

    /**
     * @param string $pointInTime
     * @return self
     */
    public static function fromDateTime(DateTimeImmutable $dateTime): self;

    /**
     * @return self
     */
    public static function now(): self;

    /**
     * @param PointInTime $pointInTime
     * @return bool
     */
    public function equals(PointInTime $pointInTime): bool;

    /**
     * @param PointInTime $pointInTime
     * @return bool
     */
    public function after(PointInTime $pointInTime): bool;

    /**
     * @param string $interval
     * @return $this
     */
    public function add(string $interval): self;

    /**
     * @param string $interval
     * @return $this
     */
    public function sub(string $interval): self;

    /**
     * @param PointInTime $pointInTime
     * @return DateInterval
     */
    public function diff(PointInTime $pointInTime): DateInterval;

    /**
     * @return DateTimeImmutable
     */
    public function dateTime(): DateTimeImmutable;

    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @return string
     */
    public function toString(): string;
}
