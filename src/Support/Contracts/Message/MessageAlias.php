<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

interface MessageAlias
{
    public function classToAlias(string $eventClass): string;

    public function instanceToAlias(object $event): string;
}
