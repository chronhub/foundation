<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests;

use Chronhub\Foundation\Reporter\Services\FoundationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCaseWithOrchestra extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [FoundationServiceProvider::class];
    }
}
