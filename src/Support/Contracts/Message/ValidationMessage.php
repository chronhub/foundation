<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

interface ValidationMessage extends Messaging
{
    /**
     * @return array
     */
    public function validationRules(): array;
}
