<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Message\DomainCommand;
use Chronhub\Foundation\Message\DomainEvent;
use Chronhub\Foundation\Message\DomainQuery;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;
use Chronhub\Foundation\Tests\Double\SomeNakedObject;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Generator;
use stdclass;

/** @coversDefaultClass \Chronhub\Foundation\Message\Message */
final class MessageTest extends TestCaseWithProphecy
{
    /**
     * @test
     * @dataProvider provideEventObjects
     */
    public function it_can_be_constructed(object $event): void
    {
        $message = new Message($event);

        $this->assertEquals($message->event(), $event);
        $this->assertCount(0, $message->headers());
        $this->assertFalse($message->has('some_header'));
        $this->assertFalse($message->isMessaging());
    }

    /**
     * @test
     * @dataProvider provideEventMessaging
     */
    public function it_can_be_constructed_with_event_messaging(Messaging $event): void
    {
        $message = new Message($event);

        $this->assertInstanceOf($message->event()::class, $event);
        $this->assertCount(0, $message->headers());
        $this->assertFalse($message->has('some_header'));
        $this->assertTrue($message->isMessaging());
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_headers(): void
    {
        $message = new Message(new stdclass, [Header::EVENT_ID => '123']);

        $this->assertTrue($message->has(Header::EVENT_ID));
        $this->assertEquals('123', $message->header(Header::EVENT_ID));
    }

    public function provideEventObjects(): Generator
    {
        yield [new stdclass];
        yield [new SomeNakedObject()];
    }

    public function provideEventMessaging(): Generator
    {
        yield [DomainCommand::fromContent(['name' => 'steph bug'])];
        yield [DomainEvent::fromContent(['name' => 'steph bug'])];
        yield [DomainQuery::fromContent(['name' => 'steph bug'])];
    }
}
