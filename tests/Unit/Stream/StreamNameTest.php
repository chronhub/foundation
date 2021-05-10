<?php

namespace Chronhub\Foundation\Tests\Unit\Stream;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Stream\StreamName;
use Chronhub\Foundation\Tests\TestCase;
use Generator;

/** @coversDefaultClass \Chronhub\Foundation\Stream\StreamName */
class StreamNameTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $streamName = new StreamName('customer_stream');

        $this->assertEquals('customer_stream', $streamName->toString());
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $streamName = new StreamName('customer_stream');

        $this->assertEquals('customer_stream', $streamName->toString());
        $this->assertEquals('customer_stream', $streamName->__toString());
        $this->assertEquals('customer_stream', (string)$streamName);
    }

    /**
     * @test
     * @param string $invalidStreamName
     * @dataProvider provideInvalidStreamName
     */
    public function it_raise_exception_when_stream_name_is_empty(string $invalidStreamName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid stream name');

        new StreamName($invalidStreamName);
    }

    public function provideInvalidStreamName(): Generator
    {
        yield [''];
        yield [' '];
    }
}
