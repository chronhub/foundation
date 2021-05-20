<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Ramsey\Uuid\Uuid;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkEventId implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        if ($message->hasNot(Header::EVENT_ID)) {
            $message = $message->withHeader(Header::EVENT_ID, Uuid::uuid4()->toString());
        }

        return $message;
    }
}
