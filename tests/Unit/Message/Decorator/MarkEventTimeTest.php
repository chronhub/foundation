<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Decorator;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Clock\UniversalSystemClock;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Message\Decorator\MarkEventTime;
use Chronhub\Foundation\Support\Contracts\Message\Header;

final class MarkEventTimeTest extends TestCaseWithProphecy
{
    /**
     * @test
     */
    public function it_set_event_time_header(): void
    {
        $clock = new UniversalSystemClock();

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']));

        $decorator = new MarkEventTime($clock);

        $messageMarked = $decorator->decorate($message);

        $this->assertNull($message->header(Header::EVENT_TIME));
        $this->assertIsString($messageMarked->header(Header::EVENT_TIME));
    }

    /**
     * @test
     */
    public function it_does_not_override_event_time_header_if_already_exists(): void
    {
        $clock = new UniversalSystemClock();
        $now = $clock->fromNow()->toString();

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), [
            Header::EVENT_TIME => $now,
        ]);

        $decorator = new MarkEventTime($clock);

        $messageMarked = $decorator->decorate($message);

        $this->assertEquals([Header::EVENT_TIME => $now], $messageMarked->headers());
    }
}
