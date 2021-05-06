<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Support\Contracts\Message\Header;

final class AsyncMessageHeader implements Header
{
    public function __construct(private bool $asyncMarker)
    {
        //
    }

    public function name(): string
    {
        return self::ASYNC_MARKER;
    }

    public function toValue(): bool
    {
        return $this->asyncMarker;
    }

    public function jsonSerialize()
    {
        return [$this->name(), $this->toValue()];
    }
}
