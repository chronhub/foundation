<?php

declare(strict_types=1);

use Chronhub\Foundation\Message\Domain;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\PromiseHandler;
use React\Promise\PromiseInterface;

if (!function_exists('clock')) {
    function clock(): Clock
    {
        return app(Clock::class);
    }
}

if (!function_exists('pointInTime')) {
    function pointInTime(): PointInTime
    {
        return app(Clock::class)->fromNow();
    }
}

if (!function_exists('handlePromise')) {
    function handlePromise(PromiseInterface $promise, bool $raisePromiseException = true): mixed
    {
        return (new PromiseHandler())->handlePromise($promise, $raisePromiseException);
    }
}

if (!function_exists('determineAggregateId')) {
    function determineAggregateId(Domain $event): ?AggregateId
    {
        $aggregateId = $event->header(Header::AGGREGATE_ID);

        if ($aggregateId instanceof AggregateId) {
            return $aggregateId;
        }

        $aggregateIdType = $event->header(Header::AGGREGATE_ID_TYPE);

        if(!is_string($aggregateId) || !is_string($aggregateIdType)){
            return null;
        }

        /* @var AggregateId $aggregateIdType */
        return $aggregateIdType::fromString($aggregateId);
    }
}
