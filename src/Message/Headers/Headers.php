<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use ArrayAccess;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Countable;
use JsonSerializable;

final class Headers implements JsonSerializable, ArrayAccess, Countable
{
    private array $headers = [];

    public function __construct(Header ...$headers)
    {
        foreach ($headers as $header) {
            $this->headers[$header->name()] = $header;
        }
    }

    public function toArray(): array
    {
        return $this->headers;
    }

    public function jsonSerialize(): array
    {
        $headers = [];

        array_walk($this->headers, function (Header $header) use (&$headers): void {
            $headers += $header->jsonSerialize();
        });

        return $headers;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->headers[$offset]);
    }

    public function offsetGet($offset): ?Header
    {
        return $this->headers[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->headers[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->headers[$offset]);
    }

    public function count(): int
    {
        return count($this->headers);
    }
}
