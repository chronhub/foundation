<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use Chronhub\Foundation\Message\Domain;

final class SomeDomain extends Domain
{
    public function type(): string
    {
        return 'domain_test';
    }
}
