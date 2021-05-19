<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Aggregate;

use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function get_class;

trait HasAggregateIdentity
{
    private UuidInterface $identifier;

    protected function __construct(UuidInterface $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function fromString(string $aggregateId): AggregateId
    {
        return new self(Uuid::fromString($aggregateId));
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

    public static function create(): AggregateId
    {
        return new self(Uuid::uuid4());
    }
}
