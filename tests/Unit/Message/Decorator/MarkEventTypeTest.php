<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Decorator;

use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Message\Decorator\MarkEventType;
use Chronhub\Foundation\Support\Contracts\Message\Header;

final class MarkEventTypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_set_event_type_header(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $decorator = new MarkEventType();

        $messageMarked = $decorator->decorate($message);

        $this->assertNull($message->header(Header::EVENT_TYPE));
        $this->assertEquals([Header::EVENT_TYPE => SomeCommand::class], $messageMarked->headers());
    }

    /**
     * @test
     */
    public function it_does_not_override_event_type_header_if_already_exists(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), [
            Header::EVENT_TYPE => 'some-command',
        ]);

        $decorator = new MarkEventType();

        $messageMarked = $decorator->decorate($message);

        $this->assertEquals([Header::EVENT_TYPE => 'some-command'], $messageMarked->headers());
    }
}
