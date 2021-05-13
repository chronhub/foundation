<?php

namespace Chronhub\Foundation\Support\Contracts\Aggregate;

use Chronhub\Foundation\Message\DomainEvent;
use Generator;

interface AggregateRoot
{
    /**
     * @param AggregateId $aggregateId
     * @param Generator<DomainEvent>   $events
     * @return AggregateRoot&static|null
     */
    public static function reconstituteFromEvents(AggregateId $aggregateId, Generator $events): ?AggregateRoot;

    /**
     * @return DomainEvent[]
     */
    public function releaseEvents(): array;

    /**
     * @return AggregateId
     */
    public function aggregateId(): AggregateId;

    /**
     * @return int
     */
    public function version(): int;
}
