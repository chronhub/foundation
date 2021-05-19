<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Payload
{
    public function __construct(array $headers = [], array $content = [], ?int $no = null)
    {
    }
}
