<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Chronhub\Foundation\Reporter\Services\ReporterServiceProvider;
use Chronhub\Foundation\Reporter\Services\ConfigurationServiceProvider;

class TestCaseWithOrchestra extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ConfigurationServiceProvider::class,
            ReporterServiceProvider::class,
        ];
    }
}
