<?php

namespace Chronhub\Foundation\Support\Contracts\Reporter;

use Chronhub\Foundation\Message\Message;

interface Router
{
    /**
     * @param Message $message
     * @return iterable<callable>
     */
    public function route(Message $message): iterable;
}
