<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Clock;

use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;
use DateTimeImmutable;

final class UniversalSystemClock implements Clock
{
    public function fromNow(): PointInTime
    {
        return UniversalPointInTime::now();
    }

    public function fromDateTime(DateTimeImmutable $dateTime): PointInTime
    {
        return UniversalPointInTime::fromDateTime($dateTime);
    }

    public function fromString(string $dateTime): PointInTime
    {
        return UniversalPointInTime::fromString($dateTime);
    }
}
