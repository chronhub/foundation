<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use Chronhub\Foundation\Message\DomainCommand;
use Chronhub\Foundation\Support\Contracts\Message\PreValidationMessage;

final class SomeCommandToPreValidate extends DomainCommand implements PreValidationMessage
{
    public function validationRules(): array
    {
        return ['name' => 'required'];
    }
}
