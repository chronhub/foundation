<?php

namespace Chronhub\Foundation\Support\Contracts\Clock;

use DateTimeImmutable;

interface Clock
{
    public function fromNow(): PointInTime;

    public function fromDateTime(DateTimeImmutable $dateTime): PointInTime;

    public function fromString(string $dateTime): PointInTime;
}
