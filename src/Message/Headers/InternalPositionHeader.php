<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Support\Contracts\Message\Header;

final class InternalPositionHeader implements Header
{
    public function __construct(private int $position)
    {
        //
    }

    public function name(): string
    {
        return self::INTERNAL_POSITION;
    }

    public function toValue(): int
    {
        return $this->position;
    }

    public function jsonSerialize(): array
    {
        return [self::INTERNAL_POSITION => $this->position];
    }
}
