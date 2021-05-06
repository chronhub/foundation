<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use JsonSerializable;

interface Content extends JsonSerializable
{
    public static function fromContent(array $content): Content;

    public function toContent(): array;
}
