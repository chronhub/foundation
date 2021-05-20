<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tests\Double\SomeQuery;

/** @coversDefaultClass \Chronhub\Foundation\Message\DomainQuery */
final class DomainQueryTest extends TestCase
{
    /**
     * @test
     */
    public function it_assert_event_type(): void
    {
        $event = SomeQuery::fromContent([]);

        $this->assertEquals('query', $event->type());
    }
}
