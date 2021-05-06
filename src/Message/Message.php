<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

use Chronhub\Foundation\Message\Headers\HasHeaders;
use Chronhub\Foundation\Message\Headers\Headers;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;

final class Message
{
    use HasHeaders;

    public function __construct(private object $event, Header ...$headers)
    {
        $this->headers = new Headers(...$headers);
    }

    public function event(): object
    {
        if ($this->event instanceof Messaging || method_exists($this->event, 'withHeaders')) {
            return $this->event->withHeaders(...$this->headers->toArray());
        }

        return $this->event;
    }

    public function withHeader(Header $header): Message
    {
        $message = clone $this;

        $message->headers[$header->name()] = $header;

        return $message;
    }

    public function isMessaging(): bool
    {
        return $this->event instanceof Messaging;
    }
}
