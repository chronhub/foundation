<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Message\Payload;
use Chronhub\Foundation\Support\Contracts\Message\Header;
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

    /**
     * @test
     * @dataProvider provideIncrement
     */
    public function it_can_be_converted_to_array(?int $increment): void
    {
        $headers = ['id' => 2];
        $content = ['name' => 'steph'];

        if ($increment) {
            $headers[Header::INTERNAL_POSITION] = $increment;
        }

        $payload = new Payload($headers, $content, $increment);

        $this->assertEquals($headers, $payload->headers());
        $this->assertEquals($content, $payload->content());

        if ($increment) {
            $this->assertEquals($increment, $payload->increment());
        }

        $this->assertEquals([
            'headers' => $headers,
            'content' => $content
        ], $payload->toArray());
    }

    /**
     * @test
     * @dataProvider provideIncrementAndInternalPositionHeader
     */
    public function it_does_not_override_internal_position_header_when_converting_payload_to_array(?int $increment, array $internalPosition): void
    {
        $this->markTestSkipped('use attribute for array payload or we should not make logic here');

        $headers = array_merge(['id' => 2], $internalPosition);
        $content = ['name' => 'steph'];

        $payload = new Payload($headers, $content, 5);

        $this->assertEquals($headers, $payload->headers());

        if (isset($headers[Header::INTERNAL_POSITION])) {
            $this->assertEquals(1, $payload->headers()[Header::INTERNAL_POSITION]);
        }

        $this->assertEquals([
            'headers' => $headers,
            'content' => $content
        ], $payload->toArray());
    }

    public function provideIncrement(): Generator
    {
        yield [null];

        yield [1];
    }

    public function provideIncrementAndInternalPositionHeader(): Generator
    {
        yield [1, []];
        yield [5, [Header::INTERNAL_POSITION => 1]];
    }
}
