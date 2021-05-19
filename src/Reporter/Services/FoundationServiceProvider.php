<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Illuminate\Support\AggregateServiceProvider;

final class FoundationServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        ConfigurationServiceProvider::class,
        ReporterServiceProvider::class,
    ];
}
