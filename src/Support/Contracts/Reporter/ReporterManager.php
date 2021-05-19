<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Reporter;

interface ReporterManager
{
    public function create(string $driver, string $type): Reporter;

    public function command(string $driver = 'default'): Reporter;

    public function event(string $driver = 'default'): Reporter;

    public function query(string $driver = 'default'): Reporter;

    public function extends(string $driver, string $type, callable $reporter): void;
}
