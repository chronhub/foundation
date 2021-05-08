<?php

namespace Chronhub\Foundation\Support\Contracts\Reporter;

use Chronhub\Foundation\Message\Message;

interface AuthorizeMessage
{
    /**
     * @param string     $event
     * @param Message    $message
     * @param mixed|null $context
     * @return bool
     */
    public function isGranted(string $event, Message $message, mixed $context = null): bool;

    /**
     * @param string     $event
     * @param Message    $message
     * @param mixed|null $context
     * @return bool
     */
    public function isNotGranted(string $event, Message $message, mixed $context = null): bool;
}
