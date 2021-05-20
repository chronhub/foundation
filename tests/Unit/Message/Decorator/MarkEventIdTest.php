<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Decorator;

use Ramsey\Uuid\Uuid;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Message\Decorator\MarkEventId;
use Chronhub\Foundation\Support\Contracts\Message\Header;

final class MarkEventIdTest extends TestCase
{
    /**
     * @test
     */
    public function it_set_event_id_header(): void
    {
        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $decorator = new MarkEventId();

        $messageMarked = $decorator->decorate($message);

        $this->assertNull($message->header(Header::EVENT_ID));
        $this->assertIsString($messageMarked->header(Header::EVENT_ID));
    }

    /**
     * @test
     */
    public function it_does_not_override_event_id_header_if_already_exists(): void
    {
        $eventId = Uuid::uuid4()->toString();

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), [
            Header::EVENT_ID => $eventId,
        ]);

        $decorator = new MarkEventId();

        $messageMarked = $decorator->decorate($message);

        $this->assertEquals([Header::EVENT_ID => $eventId], $messageMarked->headers());
    }
}
