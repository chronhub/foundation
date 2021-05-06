<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

class DomainQuery extends Domain
{
    public function type(): string
    {
        return self::QUERY;
    }
}
