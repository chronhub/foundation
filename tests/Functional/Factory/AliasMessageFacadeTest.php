<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional\Factory;

use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Support\Facade\AliasMessage;
use Chronhub\Foundation\Message\Alias\AliasFromInflector;
use Chronhub\Foundation\Tests\OrchestraWithDefaultConfig;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;

final class AliasMessageFacadeTest extends OrchestraWithDefaultConfig
{
    /**
     * @test
     */
    public function it_test_facade(): void
    {
        $this->assertEquals('foundation.message.alias', AliasMessage::SERVICE_NAME);
        $this->assertTrue($this->app->bound('foundation.message.alias'));
        $this->assertInstanceOf(MessageAlias::class, $this->app->get('foundation.message.alias'));
    }

    /**
     * @test
     */
    public function it_test_service(): void
    {
        $this->assertEquals(AliasFromInflector::class, $this->app->get('foundation.message.alias')::class);
        $this->assertEquals('some-command', AliasMessage::classToAlias(SomeCommand::class));
        $this->assertEquals('some-command', AliasMessage::instanceToAlias(SomeCommand::fromContent([])));
    }
}
