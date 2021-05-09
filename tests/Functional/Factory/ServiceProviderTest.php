<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional\Factory;

use Chronhub\Foundation\Reporter\Services\ConfigurationServiceProvider;
use Chronhub\Foundation\Reporter\Services\ReporterManager;
use Chronhub\Foundation\Reporter\Services\ReporterServiceProvider;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Chronhub\Foundation\Support\Facade\AliasMessage;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Tests\TestCaseWithOrchestra;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Support\DeferrableProvider;

final class ServiceProviderTest extends TestCaseWithOrchestra
{
    /**
     * @test
     */
    public function it_fix_config(): void
    {
        $config = $this->app[Repository::class]->get('reporter');

        $this->assertIsArray($config);

        $this->assertArrayHasKey('clock', $config);
        $this->assertArrayHasKey('messaging', $config);
        $this->assertArrayHasKey('reporting', $config);
    }

    /**
     * @test
     */
    public function it_test_bindings(): void
    {
        $this->assertTrue($this->app->providerIsLoaded(ReporterServiceProvider::class));
        $this->assertInstanceOf(DeferrableProvider::class, $this->app->getProvider(ReporterServiceProvider::class));
        $this->assertTrue($this->app->providerIsLoaded(ConfigurationServiceProvider::class));

        $this->assertTrue($this->app->bound(ReporterManager::class));
        $this->assertTrue($this->app->isShared(ReporterManager::class));
        $this->assertTrue($this->app->bound(Report::SERVICE_NAME));

        $this->assertTrue($this->app->bound(Clock::class));
        $this->assertTrue($this->app->bound(MessageFactory::class));
        $this->assertTrue($this->app->bound(MessageSerializer::class));
        $this->assertTrue($this->app->bound(MessageAlias::class));
        $this->assertTrue($this->app->bound(AliasMessage::SERVICE_NAME));
    }
}
