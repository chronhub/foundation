<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Message;

interface MessageFactory
{
    public function createFrom(array|object $event): Message;
}
