<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Message\Payload;
use Chronhub\Foundation\Tests\TestCase;
use Generator;

/** @coversDefaultClass \Chronhub\Foundation\Message\Payload */
final class PayloadTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideIncrement
     */
    public function it_can_be_constructed(?int $increment): void
    {
        $headers = ['id' => 2];
        $content = ['name' => 'steph'];

        $payload = new Payload($headers, $content, $increment);

        $this->assertEquals($headers, $payload->headers());
        $this->assertEquals($content, $payload->content());

        null === $increment
            ? $this->assertNull($payload->increment())
            : $this->assertEquals(1, $payload->increment());
    }

    public function provideIncrement(): Generator
    {
        yield [null];

        yield [1];
    }
}
