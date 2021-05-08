<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;

interface MessageProducer
{
    /**
     * @param Message $message
     * @return bool
     */
    public function isSync(Message $message): bool;

    /**
     * @param Message $message
     * @return Message
     */
    public function produce(Message $message): Message;
}
