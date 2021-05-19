<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Exception;

use Chronhub\Foundation\Support\Contracts\Exception\FoundationException;

class RuntimeException extends \RuntimeException implements FoundationException
{
}
