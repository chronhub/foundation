<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;

interface MessageProducer
{
    public function isSync(Message $message): bool;

    public function produce(Message $message): Message;
}
