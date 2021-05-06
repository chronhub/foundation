<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Aggregate;

use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait HasAggregateId
{
    private UuidInterface $identifier;

    protected function __construct(UuidInterface $identifier)
    {
        $this->identifier = $identifier;
    }

    public function toString(): string
    {
        return $this->identifier->toString();
    }

    public function equalsTo(AggregateId $rootId): bool
    {
        return static::class === get_class($rootId)
            && $this->toString() === $rootId->toString();
    }

    /**
     * @return AggregateId|static
     */
    public static function create(): static|AggregateId
    {
        return new static(Uuid::uuid4());
    }

    /**
     * @param string $aggregateId
     * @return AggregateId|static
     */
    public static function fromString(string $aggregateId): AggregateId
    {
        return new static(Uuid::fromString($aggregateId));
    }
}
