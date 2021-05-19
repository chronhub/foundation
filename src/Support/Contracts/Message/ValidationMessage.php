<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Message;

interface ValidationMessage extends Messaging
{
    public function validationRules(): array;
}
