<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;
use function get_class;

final class MarkEventType implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        if ($message->hasNot(Header::EVENT_TYPE)) {
            $message = $message->withHeader(Header::EVENT_TYPE, get_class($message->event()));
        }

        return $message;
    }
}
