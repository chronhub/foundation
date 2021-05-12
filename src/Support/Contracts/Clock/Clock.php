<?php

namespace Chronhub\Foundation\Support\Contracts\Clock;

use DateTimeImmutable;

interface Clock
{
    /**
     * @return PointInTime
     */
    public function fromNow(): PointInTime;

    /**
     * @param DateTimeImmutable $dateTime
     * @return PointInTime
     */
    public function fromDateTime(DateTimeImmutable $dateTime): PointInTime;

    /**
     * @param string $dateTime
     * @return PointInTime
     */
    public function fromString(string $dateTime): PointInTime;
}
