<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Decorator;

use stdClass;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Message\Decorator\MarkAsync;
use Chronhub\Foundation\Support\Contracts\Message\Header;

final class MarkAsyncTest extends TestCase
{
    /**
     * @test
     */
    public function it_set_event_async_marker_header(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $decorator = new MarkAsync();

        $messageMarked = $decorator->decorate($message);

        $this->assertNull($message->header(Header::ASYNC_MARKER));
        $this->assertFalse($messageMarked->header(Header::ASYNC_MARKER));
    }

    /**
     * @test
     */
    public function it_does_not_override_event_async_marker_header_if_already_exists(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), [
            Header::ASYNC_MARKER => true,
        ]);

        $decorator = new MarkAsync();

        $messageMarked = $decorator->decorate($message);

        $this->assertTrue($message->header(Header::ASYNC_MARKER));
        $this->assertTrue($messageMarked->header(Header::ASYNC_MARKER));
    }

    /**
     * @test
     */
    public function it_does_not_mark_async_marker_header_with_no_messaging_event(): void
    {
        $message = new Message(new stdClass());

        $decorator = new MarkAsync();

        $messageMarked = $decorator->decorate($message);

        $this->assertEquals($message, $messageMarked);
        $this->assertFalse($messageMarked->has(Header::ASYNC_MARKER));
    }
}
