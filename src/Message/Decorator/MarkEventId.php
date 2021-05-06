<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Headers\IdentityHeader;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkEventId implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        $eventId = $message->eventId();

        if (null === $eventId) {
            $message = $message->withHeader(IdentityHeader::create());
        }

        return $message;
    }
}
