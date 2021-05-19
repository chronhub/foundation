<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Reporter;

use Chronhub\Foundation\Message\Message;

interface Router
{
    /**
     * @return iterable<callable>
     */
    public function route(Message $message): iterable;
}
