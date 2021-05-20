<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Aggregate;

use Generator;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Aggregate\GenericAggregateId;
use Chronhub\Foundation\Tests\Double\SomeAggregateRoot;
use Chronhub\Foundation\Tests\Double\SomeAggregateChanged;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Tests\Double\SomeAggregateRootWithApply;

final class AggregateRootTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_instantiated(): void
    {
        $aggregateId = GenericAggregateId::create();
        $aggregateRoot = SomeAggregateRoot::create($aggregateId, []);

        $this->assertEquals(0, $aggregateRoot->version());
        $this->assertCount(0, $aggregateRoot->releaseEvents());
        $this->assertEquals(0, $aggregateRoot->countEventsToRelease());
        $this->assertEquals($aggregateId, $aggregateRoot->aggregateId());
    }

    /**
     * @test
     */
    public function it_record_events(): void
    {
        $aggregateId = GenericAggregateId::create();

        $events = iterator_to_array($this->provideEvents($aggregateId, 2));

        $aggregateRoot = SomeAggregateRoot::create($aggregateId, $events);

        $this->assertEquals(2, $aggregateRoot->version());
        $this->assertEquals(2, $aggregateRoot->countEventsToRelease());
    }

    /**
     * @test
     */
    public function it_apply_events_with_default_method(): void
    {
        $aggregateId = GenericAggregateId::create();

        $events = iterator_to_array($this->provideEvents($aggregateId, 2));

        $aggregateRoot = SomeAggregateRootWithApply::create($aggregateId, $events);

        $this->assertEquals(2, $aggregateRoot->applies());
    }

    /**
     * @test
     */
    public function it_release_events(): void
    {
        $aggregateId = GenericAggregateId::create();

        $events = iterator_to_array($this->provideEvents($aggregateId, 2));

        $aggregateRoot = SomeAggregateRoot::create($aggregateId, $events);

        $this->assertEquals(2, $aggregateRoot->version());
        $this->assertEquals(2, $aggregateRoot->countEventsToRelease());

        $releaseEvents = $aggregateRoot->releaseEvents();

        $this->assertEquals(0, $aggregateRoot->countEventsToRelease());
        $this->assertEquals($events, $releaseEvents);
    }

    /**
     * @test
     */
    public function it_reconstitute_aggregate_root_from_events(): void
    {
        $aggregateId = GenericAggregateId::create();

        $events = $this->provideEvents($aggregateId, 5);

        $aggregateRoot = SomeAggregateRoot::reconstituteFromEvents($aggregateId, $events);

        $this->assertEquals(5, $aggregateRoot->version());
    }

    /**
     * @test
     */
    public function it_return_null_from_reconstitute_aggregate_with_empty_events(): void
    {
        $aggregateId = GenericAggregateId::create();

        $events = $this->provideEvents($aggregateId, 0);

        $aggregateRoot = SomeAggregateRoot::reconstituteFromEvents($aggregateId, $events);

        $this->assertNull($aggregateRoot);
    }

    private function provideEvents(AggregateId $aggregateId, int $limit): Generator
    {
        $return = $limit;

        while (0 !== $limit) {
            yield SomeAggregateChanged::withData($aggregateId, []);

            --$limit;
        }

        return $return;
    }
}
