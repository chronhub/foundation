<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Support;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\NoOpMessageDecorator;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCase;

final class NoOpMessageDecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_same_message(): void
    {
        $decorator = new NoOpMessageDecorator();

        $event = SomeCommand::fromContent(['name' => 'steph']);

        $message = new Message($event, []);

        $this->assertEquals($message, $decorator->decorate($message));
    }
}
