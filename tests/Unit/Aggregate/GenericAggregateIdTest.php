<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Aggregate;

use Chronhub\Foundation\Aggregate\GenericAggregateId;
use Chronhub\Foundation\Aggregate\HasAggregateId;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Tests\Double\SomeAggregateId;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Aggregate\GenericAggregateId */
/** @coversDefaultClass HasAggregateId */
final class GenericAggregateIdTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_instantiated(): void
    {
        $aggregateId = GenericAggregateId::create();

        $this->assertInstanceOf(AggregateId::class, $aggregateId);
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_with_string_id(): void
    {
        $stringId = GenericAggregateId::create()->toString();

        $this->assertEquals($stringId, GenericAggregateId::fromString($stringId)->toString());
    }

    /**
     * @test
     */
    public function it_can_be_compared_on_equality(): void
    {
        $aggregateId = GenericAggregateId::create();

        $fromAggregateId = GenericAggregateId::fromString($aggregateId->toString());

        $this->assertEquals($aggregateId, $fromAggregateId);
        $this->assertTrue($aggregateId->equalsTo($fromAggregateId));

        $anotherAggregateId = GenericAggregateId::fromString($aggregateId->toString());

        $this->assertequals($aggregateId, $anotherAggregateId);
        $this->assertTrue($aggregateId->equalsTo($anotherAggregateId));
    }

    /**
     * @test
     */
    public function it_can_be_compared_on_inequality(): void
    {
        $aggregateId = GenericAggregateId::create();
        $otherAggregateId = SomeAggregateId::create();

        $this->assertNotEquals($aggregateId, $otherAggregateId);
        $this->assertFalse($aggregateId->equalsTo($otherAggregateId));
    }

    /**
     * @test
     */
    public function it_can_be_compared_on_inequality_2(): void
    {
        $aggregateId = GenericAggregateId::create();
        $otherAggregateId = SomeAggregateId::fromString($aggregateId->toString());

        $this->assertNotEquals($aggregateId, $otherAggregateId);
        $this->assertFalse($aggregateId->equalsTo($otherAggregateId));
    }
}
