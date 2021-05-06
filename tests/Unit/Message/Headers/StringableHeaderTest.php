<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Message\Headers\StringableHeader;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Message\Headers\StringableHeader */
final class StringableHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $header = new StringableHeader(Header::BUS_NAME, 'reporter.default');

        $this->assertEquals(Header::BUS_NAME, $header->name());
        $this->assertEquals('reporter.default', $header->toValue());
        $this->assertEquals([Header::BUS_NAME => $header->toValue()], $header->jsonSerialize());
    }
}
