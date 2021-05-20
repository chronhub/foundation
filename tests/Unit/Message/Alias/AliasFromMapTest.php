<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Alias;

use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Message\Alias\AliasFromMap;
use Chronhub\Foundation\Exception\InvalidArgumentException;

/** @coversDefaultClass \Chronhub\Foundation\Message\Alias\AliasFromMap */
class AliasFromMapTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_alias_from_event_string(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);

        $map = [$event::class => 'message_alias'];

        $messageAlias = new AliasFromMap($map);

        $this->assertEquals('message_alias', $messageAlias->classToAlias($event::class));
    }

    /**
     * @test
     */
    public function it_raise_exception_when_event_class_string_does_not_exists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event class invalid_event does not exists');

        $messageAlias = new AliasFromMap([]);

        $messageAlias->classToAlias('invalid_event');
    }

    /**
     * @test
     */
    public function it_raise_exception_when_event_class_not_found_in_map(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event class ' . SomeCommand::class . ' not found in alias map');

        $messageAlias = new AliasFromMap([]);

        $messageAlias->classToAlias(SomeCommand::class);
    }

    /**
     * @test
     */
    public function it_return_alias_from_event_object(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);

        $map = [$event::class => 'message_alias'];

        $messageAlias = new AliasFromMap($map);

        $this->assertEquals('message_alias', $messageAlias->instanceToAlias($event));
    }
}
