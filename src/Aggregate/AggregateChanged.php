<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Aggregate;

use Chronhub\Foundation\Message\DomainEvent;

abstract class AggregateChanged extends DomainEvent
{
    private ?string $aggregateId;

    public static function occur(string $aggregateId, array $content): static
    {
        $self = new static($content);

        $self->aggregateId = $aggregateId;

        return $self;
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }
}
