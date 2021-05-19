<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;

interface MessageDecorator
{
    public function decorate(Message $message): Message;
}
