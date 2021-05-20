<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Producer;

use stdClass;
use Generator;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Tests\Double\SomeAsyncCommand;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageQueue;
use Chronhub\Foundation\Message\Producer\AsyncAllMessageProducer;

/** @coversDefaultClass \Chronhub\Foundation\Message\Producer\AsyncAllMessageProducer */
final class AsyncAllMessageProducerTest extends TestCaseWithProphecy
{
    private ObjectProphecy|MessageQueue $producer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->producer = $this->prophesize(MessageQueue::class);
    }

    /**
     * @test
     * @dataProvider provideSync
     */
    public function it_produce_message_synchronously(object $event): void
    {
        $message = new Message($event);

        $this->producer->toQueue($message)->shouldNotBeCalled();

        $producer = new AsyncAllMessageProducer($this->producer->reveal());

        $this->assertTrue($producer->isSync($message));

        $this->assertEquals($message, $producer->produce($message));
    }

    /**
     * @test
     * @dataProvider provideAsync
     */
    public function it_produce_message_asynchronously(object $event): void
    {
        $message = new Message($event);

        $this->producer->toQueue(Argument::type(Message::class))->shouldBeCalled();

        $producer = new AsyncAllMessageProducer($this->producer->reveal());

        $this->assertFalse($producer->isSync($message));

        $asyncMessage = $producer->produce($message);

        $this->assertNotEquals($message, $asyncMessage);

        $this->assertTrue($asyncMessage->header(Header::ASYNC_MARKER));
    }

    /**
     * @test
     */
    public function it_raise_exception_when_async_marker_header_does_not_exists_from_is_sync_method(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Async marker header is required to produce message sync/async for event');

        $producer = new AsyncAllMessageProducer($this->producer->reveal());

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $producer->isSync($message);
    }

    /**
     * @test
     */
    public function it_raise_exception_when_async_marker_header_does_not_exists_from_produce_method(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Async marker header is required to produce message sync/async for event');

        $producer = new AsyncAllMessageProducer($this->producer->reveal());

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $producer->produce($message);
    }

    public function provideSync(): Generator
    {
        yield [new stdClass()];

        $event = (SomeCommand::fromContent(['name' => 'steph']));

        yield [$event->withHeader(Header::ASYNC_MARKER, true)];
    }

    public function provideAsync(): Generator
    {
        yield [(SomeCommand::fromContent(['name' => 'steph']))->withHeader(Header::ASYNC_MARKER, false)];

        yield [(SomeAsyncCommand::fromContent(['name' => 'steph']))->withHeader(Header::ASYNC_MARKER, false)];
    }
}
