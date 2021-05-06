<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Alias;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Message\Alias\AliasFromClassname;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Message\Alias\AliasFromClassname */
class AliasFromClassnameTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_event_class_from_event_string(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);

        $messageAlias = new AliasFromClassname();

        $this->assertEquals($event::class, $messageAlias->classToAlias($event::class));
    }

    /**
     * @test
     */
    public function it_raise_exception_when_event_class_string_does_not_exists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Event class invalid_event does not exists");

        $messageAlias = new AliasFromClassname();

        $messageAlias->classToAlias('invalid_event');
    }

    /**
     * @test
     */
    public function it_return_event_class_from_event_object(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);

        $messageAlias = new AliasFromClassname();

        $this->assertEquals($event::class, $messageAlias->instanceToAlias($event));
    }
}
