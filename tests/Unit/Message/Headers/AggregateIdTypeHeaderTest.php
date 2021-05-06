<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Message\Headers\AggregateIdTypeHeader;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\Double\SomeAggregateId;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Message\Headers\AggregateIdTypeHeader */
final class AggregateIdTypeHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $header = new AggregateIdTypeHeader(SomeAggregateId::class);

        $this->assertEquals(Header::AGGREGATE_ID_TYPE, $header->name());
        $this->assertEquals(SomeAggregateId::class, $header->toValue());
        $this->assertEquals([Header::AGGREGATE_ID_TYPE => SomeAggregateId::class], $header->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_raise_exception_when_aggregate_id_type_is_not_subclass_of_aggregate_id_interface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid string aggregate type');

        new AggregateIdTypeHeader('invalid_type');
    }
}
