<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static create(string $driver, string $type)
 * @method static extends(string $driver, callable $reporter)
 * @method static command(string $driver = 'default')
 * @method static event(string $driver = 'default')
 * @method static query(string $driver = 'default')
 */
final class Report extends Facade
{
    public const SERVICE_NAME = 'foundation.reporter.manager';

    protected static function getFacadeAccessor(): string
    {
        return self::SERVICE_NAME;
    }
}
