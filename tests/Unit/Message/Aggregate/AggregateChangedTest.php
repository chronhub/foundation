<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Aggregate;

use Chronhub\Foundation\Tests\Double\SomeAggregateChanged;
use Chronhub\Foundation\Tests\Double\SomeAggregateId;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Aggregate\AggregateChanged */
final class AggregateChangedTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_instantiated(): void
    {
        $aggregateId = SomeAggregateId::create();

        $event = SomeAggregateChanged::withData($aggregateId, ['name' => 'steph']);

        $this->assertEquals($aggregateId->toString(), $event->aggregateId());
        $this->assertEquals(['name' => 'steph'], $event->toContent());
    }
}
