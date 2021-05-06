<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Support\Contracts\Message\Header;

final class StringableHeader implements Header
{
    public function __construct(private string $name, private string $value)
    {
        //
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toValue(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [$this->name() => $this->toValue()];
    }
}
