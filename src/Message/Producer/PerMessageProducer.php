<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Producer;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\AsyncMessage;

final class PerMessageProducer extends AbstractMessageProducer
{
    protected function isSyncWithStrategy(Message $message): bool
    {
        return ! $message->event() instanceof AsyncMessage;
    }
}
