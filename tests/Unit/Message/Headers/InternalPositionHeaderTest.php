<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Message\Headers\InternalPositionHeader;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Message\Headers\InternalPositionHeader */
final class InternalPositionHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $header = new InternalPositionHeader(5);

        $this->assertEquals(5, $header->toValue());
        $this->assertEquals(Header::INTERNAL_POSITION, $header->name());
        $this->assertEquals([Header::INTERNAL_POSITION => 5], $header->jsonSerialize());
    }
}
