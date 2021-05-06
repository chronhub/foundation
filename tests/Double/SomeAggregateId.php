<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use Chronhub\Foundation\Aggregate\HasAggregateId;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;

final class SomeAggregateId implements AggregateId
{
    use HasAggregateId;
}
