<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Aggregate;

use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;

final class GenericAggregateId implements AggregateId
{
    use HasAggregateIdentity;
}
