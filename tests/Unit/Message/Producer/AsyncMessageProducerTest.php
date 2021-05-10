<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Producer;

use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Message\Producer\AsyncMessageProducer;
use Chronhub\Foundation\Message\Producer\IlluminateProducer;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\Double\SomeAsyncCommand;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Generator;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

final class AsyncMessageProducerTest extends TestCaseWithProphecy
{
    private ObjectProphecy|IlluminateProducer $illuminateProducer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->illuminateProducer = $this->prophesize(IlluminateProducer::class);
    }

    /**
     * @test
     * @dataProvider provideSync
     *
     * @param object $event
     * @param string $strategy
     */
    public function it_produce_message_synchronously(object $event, string $strategy): void
    {
        $message = new Message($event);

        $this->illuminateProducer->toQueue($message)->shouldNotBeCalled();

        $producer = new AsyncMessageProducer($this->illuminateProducer->reveal(), $strategy);

        $this->assertTrue($producer->isSync($message));

        $this->assertEquals($message, $producer->produce($message));
    }

    /**
     * @test
     * @dataProvider provideAsync
     *
     * @param object $event
     * @param string $strategy
     */
    public function it_produce_message_asynchronously(object $event, string $strategy): void
    {
        $message = new Message($event);

        $this->illuminateProducer->toQueue(Argument::type(Message::class))->shouldBeCalled();

        $producer = new AsyncMessageProducer($this->illuminateProducer->reveal(), $strategy);

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

        $producer = new AsyncMessageProducer($this->illuminateProducer->reveal(), 'no matter');

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

        $producer = new AsyncMessageProducer($this->illuminateProducer->reveal(), 'no matter');

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $producer->produce($message);
    }

    /**
     * @test
     */
    public function it_raise_exception_with_invalid_strategy_type(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid producer strategy invalid strategy');

        $producer = new AsyncMessageProducer($this->illuminateProducer->reveal(), 'invalid strategy');

        $headers = [Header::ASYNC_MARKER => false];
        $message = new Message(SomeAsyncCommand::fromContent(['name' => 'steph']), $headers);

        $producer->isSync($message);
    }

    public function provideSync(): Generator
    {
        yield [new stdClass(), 'no matter'];

        $event = (SomeCommand::fromContent(['name' => 'steph']));

        yield [$event->withHeader(Header::ASYNC_MARKER, true), 'no matter'];

        yield [$event->withHeader(Header::ASYNC_MARKER, false), 'sync'];
    }

    public function provideAsync(): Generator
    {
        yield [(SomeCommand::fromContent(['name' => 'steph']))->withHeader(Header::ASYNC_MARKER, false), 'async'];

        yield [(SomeAsyncCommand::fromContent(['name' => 'steph']))->withHeader(Header::ASYNC_MARKER, false), 'per_message'];
    }
}
