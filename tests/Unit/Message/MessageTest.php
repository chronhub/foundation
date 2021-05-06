<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Message\DomainCommand;
use Chronhub\Foundation\Message\DomainEvent;
use Chronhub\Foundation\Message\DomainQuery;
use Chronhub\Foundation\Message\Message;
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
        $message = new Message(new stdclass);

        $this->assertTrue($message->has(Header::EVENT_ID));
        $this->assertEquals($message->header(Header::EVENT_ID), $header->reveal());
    }

    /**
     * @test
     */
    public function it_override_header_with_new_message_instance(): void
    {
        $eventIdOne = $this->prophesize(HeadingId::class);
        $eventIdOne->name()->willReturn(HeadingId::EVENT_ID)->shouldBeCalled();

        $eventIdTwo = $this->prophesize(HeadingId::class);
        $eventIdTwo->name()->willReturn(HeadingId::EVENT_ID)->shouldBeCalled();

        $message = new Message(new stdclass, $eventIdOne->reveal());

        $this->assertEquals($message->header(Header::EVENT_ID), $eventIdOne->reveal());

        $newMessage = $message->withHeader($eventIdTwo->reveal());

        $this->assertNotSame($message, $newMessage);

        $this->assertEquals($message->header(Header::EVENT_ID), $eventIdTwo->reveal());
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
