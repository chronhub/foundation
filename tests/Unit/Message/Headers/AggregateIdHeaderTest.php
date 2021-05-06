<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Message\Headers\AggregateIdHeader;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\Double\SomeAggregateId;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Message\Headers\AggregateIdHeader */
final class AggregateIdHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_instantiated_from_type(): void
    {
        $aggregateId = SomeAggregateId::create();

        $header = AggregateIdHeader::fromType($aggregateId->toString(), $aggregateId::class);

        $this->assertEquals(Header::AGGREGATE_ID, $header->name());
        $this->assertEquals($aggregateId, $header->toValue());
        $this->assertEquals([Header::AGGREGATE_ID => $aggregateId->toString()], $header->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_raise_exception_when_aggregate_id_type_is_not_subclass_of_aggregate_id_interface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid string aggregate type');

        AggregateIdHeader::fromType('123-456', 'invalid_type');
    }
}
