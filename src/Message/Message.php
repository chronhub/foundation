<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

use Chronhub\Foundation\Support\Contracts\Message\Messaging;

final class Message
{
    public function __construct(private object $event, private array $headers = [])
    {

    }

    public function event(): object
    {
        if ($this->event instanceof Messaging || method_exists($this->event, 'withHeaders')) {
            return $this->event->withHeaders(...$this->headers);
        }

        return $this->event;
    }

    public function withHeader(string $key, $header): Message
    {
        $message = clone $this;

        $message->headers[$key] = $header;

        return $message;
    }

    public function has(string $headerKey): bool
    {
        return isset($this->headers[$headerKey]);
    }

    public function isMessaging(): bool
    {
        return $this->event instanceof Messaging;
    }

    public function headers(): array
    {
        return $this->headers;
    }
}
