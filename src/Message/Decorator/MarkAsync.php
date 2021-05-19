<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkAsync implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        if ( ! $message->isMessaging()) {
            return $message;
        }

        if ($message->hasNot(Header::ASYNC_MARKER)) {
            $message = $message->withHeader(Header::ASYNC_MARKER, false);
        }

        return $message;
    }
}
