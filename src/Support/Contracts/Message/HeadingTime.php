<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

interface HeadingTime extends Header
{
    public static function now(): HeadingTime;

    public static function fromString(string $time): HeadingTime;

    public function format(?string $format = null): string;
}
