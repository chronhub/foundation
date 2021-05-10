<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

use Chronhub\Foundation\Support\Contracts\Message\Header;

final class Payload
{
    public function __construct(private array $headers,
                                private array $content,
                                private ?int $increment)
    {
        //
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function content(): array
    {
        return $this->content;
    }

    public function increment(): ?int
    {
        return $this->increment;
    }

    public function toArray(): array
    {
        $payload = [
            'headers' => $this->headers,
            'content' => $this->content,
        ];

        if (null !== $this->increment && !isset($this->headers[Header::INTERNAL_POSITION])) {
            $payload[Header::INTERNAL_POSITION] = $this->increment;
        }

        return $payload;
    }
}
