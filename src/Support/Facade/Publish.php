<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Facade;

use Illuminate\Support\Facades\Facade;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;

/**
 * @method static command(array|object $command)
 * @method static event(array|object $event)
 * @method static query(array|object $query)
 * @method static queryHandled(array|object $query)
 * @method static withDriver(string $driver)                                default as 'default'
 * @method static withSubscribers(string|MessageSubscriber ...$subscribers)
 * @method static withRaisePromiseException(bool $raisePromiseException)    default to true
 */
final class Publish extends Facade
{
    const SERVICE_NAME = 'reporter.publisher';

    protected static function getFacadeAccessor(): string
    {
        /*
         * Do not act as singleton
         *
         * @see https://github.com/laravel/ideas/issues/1088
         */
        self::clearResolvedInstance(self::SERVICE_NAME);

        return self::SERVICE_NAME;
    }
}
