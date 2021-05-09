<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional\Factory;

use Chronhub\Foundation\Message\Alias\AliasFromInflector;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Facade\AliasMessage;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithOrchestra;

final class AliasMessageFacadeTest extends TestCaseWithOrchestra
{
    /**
     * @test
     */
    public function it_test_facade(): void
    {
        $this->assertEquals('chronhub.message.alias', AliasMessage::SERVICE_NAME);
        $this->assertTrue($this->app->bound('chronhub.message.alias'));
        $this->assertInstanceOf(MessageAlias::class, $this->app->get('chronhub.message.alias'));
    }

    /**
     * @test
     */
    public function it_test_service(): void
    {
        $this->assertEquals(AliasFromInflector::class, $this->app->get('chronhub.message.alias')::class);
        $this->assertEquals('some-command', AliasMessage::classToAlias(SomeCommand::class));
        $this->assertEquals('some-command', AliasMessage::instanceToAlias(SomeCommand::fromContent([])));
    }
}
