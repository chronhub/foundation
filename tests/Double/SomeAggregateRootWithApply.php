<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use RuntimeException;
use Chronhub\Foundation\Aggregate\AggregateChanged;
use Chronhub\Foundation\Aggregate\HasAggregateRoot;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateRoot;

final class SomeAggregateRootWithApply implements AggregateRoot
{
    use HasAggregateRoot;

    private int $applies = 0;

    public static function create(AggregateId $aggregateId, array $events): self
    {
        $aggregateRoot = new static($aggregateId);

        foreach ($events as $event) {
            if ( ! $event instanceof AggregateChanged) {
                $exceptionMessage = sprintf(
                    'Current test class %s only support %s event type',
                    SomeAggregateRootWithApply::class, AggregateChanged::class
                );

                throw new RuntimeException($exceptionMessage);
            }

            $aggregateRoot->recordThat($event);
        }

        return $aggregateRoot;
    }

    public function applies(): int
    {
        return $this->applies;
    }

    protected function applySomeAggregateChanged(AggregateChanged $event): void
    {
        ++$this->applies;
    }
}
