<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Stream;

use Generator;
use Illuminate\Support\LazyCollection;

final class Stream
{
    private LazyCollection $events;

    public function __construct(private StreamName $streamName, iterable $events = [])
    {
        $this->events = new LazyCollection($events);
    }

    public function name(): StreamName
    {
        return $this->streamName;
    }

    public function events(): Generator
    {
        yield from $this->events;

        return $this->events->count();
    }
}
