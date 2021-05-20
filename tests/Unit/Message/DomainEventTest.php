<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tests\Double\SomeEvent;

/** @coversDefaultClass \Chronhub\Foundation\Message\DomainEvent */
final class DomainEventTest extends TestCase
{
    /**
     * @test
     */
    public function it_assert_event_type(): void
    {
        $event = SomeEvent::fromContent([]);

        $this->assertEquals('event', $event->type());
    }
}
