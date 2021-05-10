<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Tests\Double\SomeEvent;
use Chronhub\Foundation\Tests\TestCase;

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
