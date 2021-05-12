<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;
use Generator;

interface MessageSerializer
{
    /**
     * @param Message $message
     * @return array
     */
    public function serializeMessage(Message $message): array;

    /**
     * @param array $payload
     * @return Generator<Message>
     */
    public function unserializeContent(array $payload): Generator;
}
