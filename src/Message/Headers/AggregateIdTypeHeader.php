<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Support\Contracts\Message\Header;

final class AggregateIdTypeHeader implements Header
{
    public function __construct(private string $aggregateType)
    {
        if (!is_subclass_of($aggregateType, AggregateId::class)) {
            throw new InvalidArgumentException('Invalid string aggregate type');
        }
    }

    public function jsonSerialize(): array
    {
        return [$this->name() => $this->aggregateType];
    }

    public function name(): string
    {
        return self::AGGREGATE_ID_TYPE;
    }

    public function toValue()
    {
        return $this->aggregateType;
    }
}
