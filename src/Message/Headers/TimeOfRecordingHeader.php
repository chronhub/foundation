<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Support\Contracts\Message\HeadingTime;
use DateTimeImmutable;
use DateTimeZone;

final class TimeOfRecordingHeader implements HeadingTime
{
    const FORMAT = 'Y-m-d\TH:i:s.u';

    public static function fromString(string $time): HeadingTime
    {
        $datetime = DateTimeImmutable::createFromFormat(self::FORMAT, $time, new DateTimeZone('utc'));

        if (false === $datetime) {
            throw new InvalidArgumentException("Invalid date time string");
        }

        return new self($datetime);
    }

    public static function now(): HeadingTime
    {
        // return clock func
        return new self(new DateTimeImmutable('now', new DateTimeZone('utc')));
    }

    public function format(?string $format = null): string
    {
        return $this->datetime->format($format ?? self::FORMAT);
    }

    public function name(): string
    {
        return self::EVENT_TIME;
    }

    public function toValue(): DateTimeImmutable
    {
        return $this->datetime;
    }

    public function jsonSerialize(): array
    {
        return [$this->name() => $this->format()];
    }

    private function __construct(private DateTimeImmutable $datetime)
    {
        //
    }
}
