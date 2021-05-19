<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Aggregate;

use Generator;
use Chronhub\Foundation\Message\DomainEvent;

interface AggregateRoot
{
    /**
     * @param Generator<DomainEvent> $events
     *
     * @return AggregateRoot&static|null
     */
    public static function reconstituteFromEvents(AggregateId $aggregateId, Generator $events): ?AggregateRoot;

    /**
     * @return DomainEvent[]
     */
    public function releaseEvents(): array;

    public function aggregateId(): AggregateId;

    public function version(): int;
}
