<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;

interface MessageQueue
{
    /**
     * @param Message $message
     */
    public function toQueue(Message $message): void;
}
