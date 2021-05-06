<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Support\Contracts\Message\HeadingId;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class IdentityHeader implements HeadingId
{
    public static function create(): HeadingId
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): HeadingId
    {
        return new self(UuidV4::fromString($id));
    }

    public function toString(): string
    {
        return $this->id->toString();
    }

    public function name(): string
    {
        return self::EVENT_ID;
    }

    public function toValue(): UuidInterface
    {
        return $this->id;
    }

    public function jsonSerialize(): array
    {
        return [$this->name() => $this->toString()];
    }

    private function __construct(private UuidInterface $id)
    {
        //
    }
}
