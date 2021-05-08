<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Support\Traits;

use React\Promise\PromiseInterface;
use Throwable;

trait HandlePromise
{
    /**
     * @param PromiseInterface $promise
     * @param bool             $raiseException
     * @return mixed
     * @throws Throwable
     */
    public function handlePromise(PromiseInterface $promise, bool $raiseException = true): mixed
    {
        $exception = null;
        $result = null;

        $promise->then(
            static function ($data) use (&$result) {
                $result = $data;
            },
            static function ($exc) use (&$exception) {
                $exception = $exc;
            }
        );

        if ($exception instanceof Throwable) {
            if ($raiseException) {
                throw $exception;
            }

            return $exception;
        }

        /* @var mixed $result */
        return $result;
    }
}
