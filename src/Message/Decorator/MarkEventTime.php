<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkEventTime implements MessageDecorator
{
    public function __construct(private Clock $clock)
    {
    }

    public function decorate(Message $message): Message
    {
        if ($message->hasNot(Header::EVENT_TIME)) {
            $message = $message->withHeader(Header::EVENT_TIME, $this->clock->fromNow());
        }

        return $message;
    }
}
