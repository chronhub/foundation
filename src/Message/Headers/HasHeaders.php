<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

trait HasHeaders
{
    protected array $headers = [];

    public function header(string $header): mixed
    {
        return $this->headers[$header] ?? null;
    }

    public function has(string $header): bool
    {
        return isset($this->headers[$header]);
    }

    public function headers(): array
    {
        return $this->headers;
    }
}
