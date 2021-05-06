<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Message\Headers\TimeOfRecordingHeader;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\TestCase;
use DateTimeImmutable;

/** @coversDefaultClass \Chronhub\Foundation\Message\Headers\TimeOfRecordingHeader */
final class TimeOfRecordingHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_assert_format_date_time(): void
    {
        $this->assertEquals('Y-m-d\TH:i:s.u', TimeOfRecordingHeader::FORMAT);
    }

    /**
     * @test
     */
    public function it_can_be_instantiated(): void
    {
        $headerTime = TimeOfRecordingHeader::now();

        $this->assertInstanceOf(DateTimeImmutable::class, $headerTime->toValue());
        $this->assertEquals([Header::EVENT_TIME => $headerTime->format()], $headerTime->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_string(): void
    {
        $headerTime = TimeOfRecordingHeader::now();

        $headerTimeString = $headerTime->format();

        $newHeaderTime = TimeOfRecordingHeader::fromString($headerTimeString);

        $this->assertEquals($newHeaderTime, $headerTime);
    }

    /**
     * @test
     */
    public function it_can_be_formatted(): void
    {
        $headerTime = TimeOfRecordingHeader::now();

        $headerTimeString = $headerTime->format(DATE_ATOM);

        $this->assertNotEquals($headerTimeString, $headerTime->format());
    }

    /**
     * @test
     */
    public function it_raise_exception_when_instantiating_with_invalid_time(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date time string');

        TimeOfRecordingHeader::fromString('invalid_datetime');
    }
}
