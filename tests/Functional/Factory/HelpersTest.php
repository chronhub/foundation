<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional\Factory;

use RuntimeException;
use React\Promise\Deferred;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Tests\OrchestraWithDefaultConfig;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;

final class HelpersTest extends OrchestraWithDefaultConfig
{
    /**
     * @test
     */
    public function it_test_clock_function(): void
    {
        $clock = clock();

        $this->assertEquals($clock, $this->app[Clock::class]);
        $this->assertEquals($this->app['config']->get('reporter.clock'), $clock::class);
    }

    /**
     * @test
     */
    public function it_test_point_in_time_function(): void
    {
        $pointInTime = $this->createMock(PointInTime::class);

        $clock = $this->createMock(Clock::class);
        $clock->expects($this->once())->method('fromNow')->willReturn($pointInTime);

        $this->app->instance(Clock::class, $clock);

        $this->assertEquals($pointInTime, pointInTime());
    }

    /**
     * @test
     */
    public function it_test_handle_promise(): void
    {
        $promise = new Deferred();

        $promise->resolve('foo');

        $this->assertEquals('foo', handlePromise($promise->promise(), true));
    }

    /**
     * @test
     */
    public function it_raise_exception_from_handle_promise(): void
    {
        $this->expectExceptionMessage('bar');

        $exception = new RuntimeException('bar');

        $promise = new Deferred();
        try {
            throw $exception;
        } catch (RuntimeException $e) {
            $promise->reject($e);
        }

        handlePromise($promise->promise(), true);
    }

    /**
     * @test
     */
    public function it_return_exception_from_handle_promise(): void
    {
        $exception = new RuntimeException('bar');

        $promise = new Deferred();

        try {
            throw $exception;
        } catch (RuntimeException $e) {
            $promise->reject($e);
        }

        $exceptionCaught = handlePromise($promise->promise(), false);

        $this->assertEquals($exception, $exceptionCaught);
    }
}
