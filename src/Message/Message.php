<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Headers\HasHeaders;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;

final class Message
{
    use HasHeaders;

    public function __construct(private object $event, array $headers = [])
    {
        $this->headers = $this->determineHeaders($event, $headers);
    }

    public function event(): object
    {
        if ($this->event instanceof Messaging) {
            return clone $this->event->withHeaders($this->headers);
        }

        return clone $this->event;
    }

    public function eventWithoutHeaders(): object
    {
        if ($this->isMessaging()) {
            return clone $this->event->withHeaders([]);
        }

        return clone $this->event;
    }

    public function withHeader(string $key, $value): Message
    {
        $message = clone $this;

        $message->headers[$key] = $value;

        return $message;
    }

    public function withHeaders(array $headers): Message
    {
        $message = clone $this;

        $message->headers = $headers;

        return $message;
    }

    public function isMessaging(): bool
    {
        return $this->event instanceof Messaging;
    }

    private function determineHeaders(object $event, array $headers): array
    {
        if (!$event instanceof Messaging || count($event->headers()) === 0) {
            return $headers;
        }

        if (count($headers) === 0) {
            return $event->headers();
        }

        if ($headers !== $event->headers()) {
            throw new RuntimeException("Invalid headers consistency for event class " . $event::class);
        }

        return $headers;
    }
}
