<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Headers\EventTypeHeader;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkEventType implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        $eventName = $message->eventName();

        if (null === $eventName) {
            $header = new EventTypeHeader($message->event()::class);

            $message = $message->withHeader($header);
        }

        return $message;
    }
}
