<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use Chronhub\Foundation\Message\DomainCommand;
use Chronhub\Foundation\Support\Contracts\Message\ValidationMessage;

final class SomeCommandToValidate extends DomainCommand implements ValidationMessage
{
    public function validationRules(): array
    {
        return ['name' => 'required'];
    }
}
