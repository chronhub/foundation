<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Support\Contracts\Message\HeadingType;

final class EventTypeHeader implements HeadingType
{
    public function __construct(private string $eventName)
    {
        //
    }

    public function name(): string
    {
        return self::EVENT_TYPE;
    }

    public function toValue(): string
    {
        return $this->eventName;
    }

    public function jsonSerialize(): array
    {
        return [$this->name() => $this->toValue()];
    }
}
