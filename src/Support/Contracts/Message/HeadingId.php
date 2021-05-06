<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Message;

interface HeadingId extends Header
{
    public static function create(): HeadingId;

    public static function fromString(string $id): HeadingId;

    public function toString(): string;
}
