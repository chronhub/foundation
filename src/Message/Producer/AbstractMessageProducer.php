<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Producer;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\Content;
use Chronhub\Foundation\Support\Contracts\Message\MessageQueue;
use Chronhub\Foundation\Support\Contracts\Message\MessageProducer;

abstract class AbstractMessageProducer implements MessageProducer
{
    public function __construct(protected MessageQueue $queueProducer)
    {
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
        if ( ! $message->event() instanceof Content) {
            return true;
        }

        if (null === $message->header(Header::ASYNC_MARKER)) {
            throw new RuntimeException('Async marker header is required to produce message sync/async for event' . $message->event()::class);
        }

        if ($this->isAlreadyProducedAsync($message)) {
            return true;
        }

        return $this->isSyncWithStrategy($message);
    }

    protected function isAlreadyProducedAsync(Message $message): bool
    {
        return true === $message->header(Header::ASYNC_MARKER);
    }

    private function produceMessageAsync(Message $message): Message
    {
        $message = $message->withHeader(Header::ASYNC_MARKER, true);

        $this->queueProducer->toQueue($message);

        return $message;
    }

    abstract protected function isSyncWithStrategy(Message $message): bool;
}
