<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Message;

use Generator;
use Chronhub\Foundation\Message\Message;

interface MessageSerializer
{
    public function serializeMessage(Message $message): array;

    /**
     * @return Generator<Message>
     */
    public function unserializeContent(array $payload): Generator;
}
