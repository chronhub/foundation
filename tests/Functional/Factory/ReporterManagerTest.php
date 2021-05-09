<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional\Factory;

use Chronhub\Foundation\Reporter\Services\ReporterManager;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;
use Chronhub\Foundation\Tests\TestCaseWithOrchestra;

final class ReporterManagerTest extends TestCaseWithOrchestra
{
    protected function defineEnvironment($app)
    {

    }

    /**
     * @test
     */
    public function it_return_service_as_singleton(): void
    {
        $reporter = $this->app[ReporterManager::class]->create('default', Messaging::COMMAND);

        $reporter2 = $this->app[ReporterManager::class]->command('default');

        $this->assertEquals($reporter, $reporter2);
    }
}
