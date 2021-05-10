<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Services;

use Chronhub\Foundation\Exception\ReportFailed;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Reporter\ReporterManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

abstract class AbstractReporterManager implements ReporterManager
{
    /**
     * @var array<string,Reporter>
     */
    protected array $reporters = [];

    /**
     * @var array<string,callable>
     */
    protected array $customerReporters = [];

    protected array $config;

    public function __construct(protected Container $container)
    {
        $this->config = $container->get(Repository::class)->get('reporter');
    }

    public function create(string $driver, string $type): Reporter
    {
        $reporterKey = $this->determineReporterKey($driver, $type);

        if ($customerReporter = ($this->customerReporters[$reporterKey] ?? null)) {
            return $customerReporter($this->container, $this->config);
        }

        $config = $this->fromReporter("reporting.$type.$driver");

        if (!is_array($config) || empty($config)) {
            throw new ReportFailed("Invalid reporter configuration with $driver driver and $type type");
        }

        return $this->reporters[$reporterKey] = $this->createReporter($type, $config);
    }

    public function command(string $driver = 'default'): Reporter
    {
        return $this->create($driver ?? 'default', Messaging::COMMAND);
    }

    public function event(string $driver = 'default'): Reporter
    {
        return $this->create($driver ?? 'default', Messaging::EVENT);
    }

    public function query(string $driver = 'default'): Reporter
    {
        return $this->create($driver ?? 'default', Messaging::QUERY);
    }

    public function extends(string $driver, string $type, callable $reporter): void
    {
        $reporterKey = $this->determineReporterKey($driver, $type);

        $this->customerReporters[$reporterKey] = $reporter;
    }

    abstract protected function createReporter(string $type, array $config): Reporter;

    protected function determineReporterKey(string $driver, string $type): string
    {
        if (!in_array($type, Messaging::TYPES, true)) {
            throw new ReportFailed("Reporter type $type is invalid");
        }

        return $type . ':' . $driver;
    }

    protected function fromReporter(string $key, $default = null): mixed
    {
        return Arr::get($this->config, $key, $default);
    }
}
