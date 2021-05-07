<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use Chronhub\Foundation\Aggregate\HasAggregateRoot;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateRoot;

final class SomeAggregateRoot implements AggregateRoot
{
    use HasAggregateRoot;
}
