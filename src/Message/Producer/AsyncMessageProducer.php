<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Producer;

use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\AsyncMessage;
use Chronhub\Foundation\Support\Contracts\Message\Content;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageProducer;

final class AsyncMessageProducer implements MessageProducer
{
    public function __construct(private IlluminateProducer $queueProducer,
                                private string $producerStrategy)
    {
        //
    }

    public function produce(Message $message): Message
    {
        if ($this->isSync($message)) {
            return $message;
        }

        return $this->produceMessageAsync($message);
    }

    public function isSync(Message $message): bool
    {
        if (!$message->event() instanceof Content) {
            return true;
        }

        if ($this->isAlreadyProducedAsync($message)) {
            return true;
        }

        return $this->isSyncWithStrategy($message);
    }

    private function isSyncWithStrategy(Message $message): bool
    {
        return match($this->producerStrategy){
          'sync' => true,
          'per_message' => !$message->event() instanceof AsyncMessage,
          'async' => false,
          'default' => throw new RuntimeException('Invalid producer strategy ' . $this->producerStrategy)
        };
    }

    private function produceMessageAsync(Message $message): Message
    {
        $message = $message->withHeader(Header::ASYNC_MARKER, true);

        $this->queueProducer->toQueue($message);

        return $message;
    }

    private function isAlreadyProducedAsync(Message $message): bool
    {
        return true === $message->header(Header::ASYNC_MARKER);
    }
}
