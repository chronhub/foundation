<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Factory;

use Chronhub\Foundation\Message\Headers\Headers;
use Chronhub\Foundation\Message\Headers\IdentityHeader;
use Chronhub\Foundation\Support\Contracts\Message\HeadingId;
use Chronhub\Foundation\Support\Contracts\Message\HeadingType;

final class BuildHeaders
{
    private array $headers = [];

    public function fromArray(array $headers)
    {

    }

    public function withEventId(HeadingId|string|null $headerId)
    {

    }

    public function withEventType()
    {

    }

}
