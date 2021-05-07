<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

trait HasHeaders
{
    /**
     * @var array<string,mixed>
     */
    protected array $headers = [];

    public function header(string $key): mixed
    {
        return $this->headers[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->headers[$key]);
    }

    public function headers(): array
    {
        return $this->headers;
    }
}
