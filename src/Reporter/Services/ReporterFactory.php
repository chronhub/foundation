<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Illuminate\Contracts\Container\Container;

final class ReporterFactory
{
    private ?string $name = null;
    private ?string $concrete = null;
    private ?string $trackerId = null;
    private ?string $handlerMethodName = null;
    private ?string $producer = null;
    private array $decorators = [];
    private array $subscribers = [];
    private array $map = [];

    private ?string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }
}
