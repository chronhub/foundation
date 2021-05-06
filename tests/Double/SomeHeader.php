<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use Chronhub\Foundation\Support\Contracts\Message\Header;

final class SomeHeader implements Header
{
    public function __construct(private string $key, private string $value)
    {
        //
    }

    public function name(): string
    {
        return $this->key;
    }

    public function toValue(): string
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return [$this->name() => $this->value];
    }
}
