<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;

interface MessageDecorator
{
    /**
     * @param Message $message
     * @return Message
     */
    public function decorate(Message $message): Message;
}
