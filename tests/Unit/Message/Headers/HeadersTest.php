<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Message\Headers\Headers;
use Chronhub\Foundation\Tests\Double\SomeHeader;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Message\Headers\Headers */
final class HeadersTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $headers = new Headers();

        $this->assertEmpty($headers->toArray());
        $this->assertEmpty($headers->jsonSerialize());

        $this->assertFalse(isset($headers['key']));
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_headers(): void
    {
        $one = new SomeHeader('one', '1');
        $two = new SomeHeader('two', '2');

        $headers = new Headers($one, $two);

        $this->assertEquals(2, $headers->count());
        $this->assertCount(2, $headers->toArray());
        $this->assertEquals(['one' => '1', 'two' => '2'], $headers->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_can_set_headers(): void
    {
        $one = new SomeHeader('one', '1');
        $headers = new Headers($one);

        $this->assertCount(1, $headers->toArray());

        $two = new SomeHeader('two', '2');
        $headers['two'] = $two;

        $this->assertCount(2, $headers->toArray());
    }

    /**
     * @test
     */
    public function it_can_override_headers(): void
    {
        $one = new SomeHeader('one', '1');
        $headers = new Headers($one);

        $this->assertCount(1, $headers->toArray());

        $two = new SomeHeader('one', '2');
        $headers['one'] = $two;

        $this->assertEquals(['one' => '2'], $headers->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_can_unset_headers(): void
    {
        $one = new SomeHeader('one', '1');
        $headers = new Headers($one);

        $this->assertCount(1, $headers->toArray());

        unset($headers['one']);

        $this->assertEmpty($headers->toArray());
    }

    /**
     * @test
     */
    public function it_access_header(): void
    {
        $headers = new Headers();

        $this->assertNull($headers['one']);

        $one = new SomeHeader('one', '1');

        $headers['one'] = $one;

        $this->assertEquals($one, $headers['one']);
    }
}
