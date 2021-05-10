<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Producer;

use Chronhub\Foundation\Message\Message;

final class AsyncAllMessageProducer extends AbstractMessageProducer
{
    protected function isSyncWithStrategy(Message $message): bool
    {
        return false;
    }
}
