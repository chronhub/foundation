<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Aggregate;

interface AggregateId
{
    /**
     * @return AggregateId&static
     */
    public static function fromString(string $aggregateId): AggregateId;

    public function toString(): string;

    public function equalsTo(AggregateId $aggregateId): bool;
}
