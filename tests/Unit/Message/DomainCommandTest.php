<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message;

use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCase;

/** @coversDefaultClass \Chronhub\Foundation\Message\DomainCommand */
final class DomainCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_assert_command_type(): void
    {
        $command = SomeCommand::fromContent([]);

        $this->assertEquals('command', $command->type());
    }
}
