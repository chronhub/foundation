<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Producer;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Message\Payload;
use Chronhub\Foundation\Message\Producer\IlluminateQueue;
use Chronhub\Foundation\Message\Producer\MessageJob;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Illuminate\Contracts\Bus\QueueingDispatcher;
use stdClass;

final class IlluminateProducerTest extends TestCaseWithProphecy
{
    /**
     * @test
     */
    public function it_handle_message_to_queue(): void
    {
        $queue = $this->prophesize(QueueingDispatcher::class);
        $serializer = $this->prophesize(MessageSerializer::class);

        $producer = new IlluminateQueue($queue->reveal(), $serializer->reveal(), 'default', 'default');

        $message = new Message(new stdClass(), [Header::BUS_NAME => 'some_bus']);

        $payload = new Payload([], ['foo' => 'bar'], null);
        $serializer->serializeMessage($message)->willReturn($payload)->shouldBeCalled();

        $job = new MessageJob([
            'headers' => [],
            'content' => ['foo' => 'bar']
        ], 'some_bus', 'default', 'default');

        $queue->dispatchToQueue($job)->shouldBeCalled();

        $producer->toQueue($message);
    }
}
