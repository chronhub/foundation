<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Decorator;

use Chronhub\Foundation\Message\Decorator\MarkEventId;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

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
        $this->assertInstanceOf(UuidInterface::class, $messageMarked->header(Header::EVENT_ID));
    }

    /**
     * @test
     */
    public function it_does_not_override_event_id_header_if_already_exists(): void
    {
        $id = Uuid::uuid4();

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), [
            Header::EVENT_ID => $id
        ]);

        $decorator = new MarkEventId();

        $messageMarked = $decorator->decorate($message);

        $this->assertEquals([Header::EVENT_ID => $id], $messageMarked->headers());
    }
}
