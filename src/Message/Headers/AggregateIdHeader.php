<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Support\Contracts\Message\Header;

final class AggregateIdHeader implements Header
{
    public static function fromType(string $aggregateId, string $aggregateType): self
    {
        if (!is_subclass_of($aggregateType, AggregateId::class)) {
            throw new InvalidArgumentException('Invalid string aggregate type');
        }

        $aggregateId = $aggregateType::fromString($aggregateId);

        return new self($aggregateId);
    }

    public function jsonSerialize(): array
    {
        return [$this->name() => $this->aggregateId->toString()];
    }

    public function name(): string
    {
        return self::AGGREGATE_ID;
    }

    public function toValue(): AggregateId
    {
        return $this->aggregateId;
    }

    private function __construct(private AggregateId $aggregateId)
    {
        //
    }
}
