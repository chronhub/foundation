<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\DomainCommand;
use Chronhub\Foundation\Message\DomainEvent;
use Chronhub\Foundation\Message\DomainQuery;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;
use Chronhub\Foundation\Tests\Double\SomeCommand;
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

    /**
     * @test
     */
    public function it_can_override_headers(): void
    {
        $message = new Message(new stdclass, [Header::EVENT_ID => '123']);

        $messageWithHeaders = $message->withHeaders([Header::EVENT_ID => '456']);

        $this->assertNotEquals($message, $messageWithHeaders);

        $this->assertEquals([Header::EVENT_ID => '123'], $message->headers());
        $this->assertEquals([Header::EVENT_ID => '456'], $messageWithHeaders->headers());
    }

    /**
     * @test
     * @dataProvider provideHeaders
     */
    public function it_return_event_messaging_with_headers(array $headers): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), $headers);

        $this->assertEquals($event->withHeaders($headers), $message->event());
    }

    /**
     * @test
     */
    public function it_return_event_messaging_without_headers(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);
        $headers = ['some' => 'header'];

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), $headers);

        $this->assertEquals($event, $message->eventWithoutHeaders());
    }

    /**
     * @test
     */
    public function it_return_event_headers_when_message_headers_is_empty_on_construct(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);
        $event = $event->withHeaders(['some' => 'header']);

        $message = new Message($event, []);

        $this->assertEquals(['some' => 'header'], $message->headers());
        $this->assertEquals(['some' => 'header'], $message->event()->headers());
    }

    /**
     * @test
     */
    public function it_return_event_headers_when_message_headers_equals_event_headers_on_construct(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);
        $event = $event->withHeaders(['some' => 'header']);

        $message = new Message($event, ['some' => 'header']);

        $this->assertEquals(['some' => 'header'], $message->headers());
        $this->assertEquals(['some' => 'header'], $message->event()->headers());
    }

    /**
     * @test
     */
    public function it_raise_exception_when_message_headers_does_not_match_event_messaging_headers(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid headers consistency for event class ' . SomeCommand::class);

        $event = SomeCommand::fromContent(['name' => 'steph']);
        $event = $event->withHeaders(['some' => 'header']);

        new Message($event, ['another' => 'header']);
    }

    /**
     * @test
     */
    public function it_return_event_not_messaging_without_headers(): void
    {
        $message = new Message(new stdclass());

        $this->assertEquals(new stdclass(), $message->eventWithoutHeaders());
    }

    public function provideEventObjects(): Generator
    {
        yield [new stdclass];
        yield [new SomeNakedObject()];
    }

    public function provideHeaders(): Generator
    {
        yield [[]];
        yield [['some' => 'header']];
    }

    public function provideEventMessaging(): Generator
    {
        yield [DomainCommand::fromContent(['name' => 'steph bug'])];
        yield [DomainEvent::fromContent(['name' => 'steph bug'])];
        yield [DomainQuery::fromContent(['name' => 'steph bug'])];
    }
}
