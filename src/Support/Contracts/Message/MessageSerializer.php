<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Message\Payload;
use Generator;

interface MessageSerializer
{
    /**
     * @param Message $message
     * @return Payload
     */
    public function serializeMessage(Message $message): Payload;

    /**
     * @param array $payload
     * @return Generator<Message>
     */
    public function unserializeContent(array $payload): Generator;
}
