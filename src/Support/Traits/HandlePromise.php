<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Traits;

use React\Promise\PromiseInterface;
use Throwable;

trait HandlePromise
{
    /**
     * @throws Throwable
     */
    public function handlePromise(PromiseInterface $promise, bool $raiseException = true): mixed
    {
        $exception = null;
        $result = null;

        $promise->then(
            static function ($data) use (&$result): void {
                $result = $data;
            },
            static function ($exc) use (&$exception): void {
                $exception = $exc;
            }
        );

        if ($exception instanceof Throwable) {
            if ($raiseException) {
                throw $exception;
            }

            return $exception;
        }

        return $result;
    }
}
