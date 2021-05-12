<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Headers\HasHeaders;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;

final class Message
{
    use HasHeaders;

    private object $event;

    public function __construct(object $event, array $headers = [])
    {
        $this->setUp($event, $headers);
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

    public function withHeader(string $key, mixed $value): Message
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

    private function setUp(object $event, array $headers): void
    {
        if ($event instanceof self) {
            throw new InvalidArgumentException("Message event can not be an instance of " . $this::class);
        }

        if (!$event instanceof Messaging || count($event->headers()) === 0) {
            $this->event = $event;
            $this->headers = $headers;
            return;
        }

        if (count($headers) === 0) {
            $this->event = $event->withHeaders([]);
            $this->headers = $event->headers();

            return;
        }

        if ($headers !== $event->headers()) {
            throw new RuntimeException("Invalid consistency headers for event class " . $event::class);
        }

        $this->event = $event->withHeaders([]);
        $this->headers = $headers;
    }
}
