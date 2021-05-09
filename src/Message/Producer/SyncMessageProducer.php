<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Producer;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\MessageProducer;

final class SyncMessageProducer implements MessageProducer
{
    public function isSync(Message $message): bool
    {
        return true;
    }

    public function produce(Message $message): Message
    {
        return $message;
    }
}
