<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Headers\TimeOfRecordingHeader;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkEventTime implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        $eventTime = $message->eventTime();

        if (null === $eventTime) {
            $message = $message->withHeader(TimeOfRecordingHeader::now());
        }

        return $message;
    }
}
