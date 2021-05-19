<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Clock;

use DateInterval;
use DateTimeZone;
use DateTimeImmutable;
use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;

final class UniversalPointInTime implements PointInTime
{
    const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s.u';

    public static function fromDateTime(DateTimeImmutable $dateTime): self
    {
        $dateTime = $dateTime->setTimezone(new DateTimeZone('UTC'));

        return new self($dateTime);
    }

    public static function fromString(string $dateTime): self
    {
        $timeZone = new DateTimeZone('UTC');

        $dateTime = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $dateTime, $timeZone);

        if ( ! $dateTime) {
            throw new InvalidArgumentException('Invalid date time');
        }

        return new self($dateTime);
    }

    public static function now(): self
    {
        $dateTime = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        return new self($dateTime);
    }

    public function equals(PointInTime $pointInTime): bool
    {
        $this->assertSamePointInTime($pointInTime);

        return $this->toString() === $pointInTime->toString();
    }

    public function after(PointInTime $pointInTime): bool
    {
        $this->assertSamePointInTime($pointInTime);

        return $this->dateTime > $pointInTime->dateTime();
    }

    public function diff(PointInTime $pointInTime): DateInterval
    {
        $this->assertSamePointInTime($pointInTime);

        return $this->dateTime->diff($pointInTime->dateTime());
    }

    public function add(string $interval): self
    {
        $datetime = $this->dateTime->add(new DateInterval($interval));

        return new self($datetime);
    }

    public function sub(string $interval): self
    {
        $datetime = $this->dateTime->sub(new DateInterval($interval));

        return new self($datetime);
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->dateTime->format(self::DATE_TIME_FORMAT);
    }

    private function assertSamePointInTime(PointInTime $pointInTime): void
    {
        if ( ! $pointInTime instanceof UniversalPointInTime) {
            throw new InvalidArgumentException('Can not operate on two different point in time classes');
        }
    }

    private function __construct(private DateTimeImmutable $dateTime)
    {
    }
}
