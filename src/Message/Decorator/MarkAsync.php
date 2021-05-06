<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Headers\AsyncMessageHeader;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkAsync implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        if (!$message->isMessaging()) {
            return $message;
        }

        if (!$message->has(Header::ASYNC_MARKER)) {
            $message->withHeader(new AsyncMessageHeader(false));
        }

        return $message;
    }
}
