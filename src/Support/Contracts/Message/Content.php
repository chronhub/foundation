<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

interface Content
{
    public static function fromContent(array $content): Content;

    public function toContent(): array;
}
