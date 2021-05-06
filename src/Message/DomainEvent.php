<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

class DomainEvent extends Domain
{
    public function type(): string
    {
        return self::EVENT;
    }
}
