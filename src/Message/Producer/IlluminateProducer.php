<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Producer;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Illuminate\Contracts\Bus\QueueingDispatcher;

class IlluminateProducer
{
    public function __construct(private QueueingDispatcher $queueingDispatcher,
                                private MessageSerializer $messageSerializer,
                                private ?string $connection = null,
                                private ?string $queue = null)
    {
    }

    public function toQueue(Message $message): void
    {
        $messageJob = $this->toMessageJob($message);

        $this->queueingDispatcher->dispatchToQueue($messageJob);
    }

    private function toMessageJob(Message $message): object
    {
        $payload = $this->messageSerializer->serializeMessage($message)->toArray();

        return new MessageJob(
            $payload,
            $message->header(Header::BUS_NAME),
            $this->connection,
            $this->queue
        );
    }
}
