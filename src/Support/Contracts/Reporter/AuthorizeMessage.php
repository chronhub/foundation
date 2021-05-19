<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Reporter;

use Chronhub\Foundation\Message\Message;

interface AuthorizeMessage
{
    public function isGranted(string $event, Message $message, mixed $context = null): bool;

    public function isNotGranted(string $event, Message $message, mixed $context = null): bool;
}
