<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

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
}
