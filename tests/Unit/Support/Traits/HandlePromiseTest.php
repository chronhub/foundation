<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Support\Traits;

use React\Promise\Deferred;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Support\Traits\HandlePromise;

final class HandlePromiseTest extends TestCase
{
    use HandlePromise;

    /**
     * @test
     */
    public function it_handle_promise(): void
    {
        $deferred = new Deferred();
        $deferred->resolve('foo');

        $result = $this->handlePromise($deferred->promise());

        $this->assertEquals('foo', $result);
    }

    /**
     * @test
     */
    public function it_raise_exception_caught_on_promise(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('foo');

        $exception = new RuntimeException('foo');

        $deferred = new Deferred();
        $deferred->reject($exception);

        $this->handlePromise($deferred->promise(), true);
    }

    /**
     * @test
     */
    public function it_return_exception_caught_on_promise(): void
    {
        $exception = new RuntimeException('foo');

        $deferred = new Deferred();
        $deferred->reject($exception);

        $result = $this->handlePromise($deferred->promise(), false);

        $this->assertEquals($exception, $result);
    }
}
