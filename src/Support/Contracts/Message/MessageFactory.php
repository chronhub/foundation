<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;

interface MessageFactory
{
    public function createFromMessage(array|object $event): Message;
}
