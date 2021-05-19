<?php

declare(strict_types=1);

use React\Promise\PromiseInterface;
use Chronhub\Foundation\Support\PromiseHandler;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;

if ( ! function_exists('clock')) {
    function clock(): Clock
    {
        return app(Clock::class);
    }
}

if ( ! function_exists('pointInTime')) {
    function pointInTime(): PointInTime
    {
        return app(Clock::class)->fromNow();
    }
}

if ( ! function_exists('handlePromise')) {
    function handlePromise(PromiseInterface $promise, bool $raisePromiseException = true): mixed
    {
        return (new PromiseHandler())->handlePromise($promise, $raisePromiseException);
    }
}
