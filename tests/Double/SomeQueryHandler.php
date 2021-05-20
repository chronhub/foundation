<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use React\Promise\Deferred;

final class SomeQueryHandler
{
    public function query(SomeQuery $query, Deferred $promise): void
    {
        $promise->resolve($query->toContent());
    }
}
