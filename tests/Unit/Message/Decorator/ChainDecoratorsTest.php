<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Decorator;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Clock\UniversalSystemClock;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Message\Decorator\MarkEventId;
use Chronhub\Foundation\Message\Decorator\MarkEventTime;
use Chronhub\Foundation\Message\Decorator\MarkEventType;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Message\Decorator\ChainDecorators;

final class ChainDecoratorsTest extends TestCaseWithProphecy
{
    /**
     * @test
     */
    public function it_chain_message_decorators(): void
    {
        $decorators = [
            new MarkEventId(),
            new MarkEventTime(new UniversalSystemClock()),
            new MarkEventType(),
        ];

        $chain = new ChainDecorators(...$decorators);

        $message = new Message(SomeCommand::fromContent(['name' => 'steph']), []);

        $messageMarked= $chain->decorate($message);

        $this->assertNotEmpty($messageMarked->headers());
        $this->assertTrue($messageMarked->has(Header::EVENT_ID));
        $this->assertTrue($messageMarked->has(Header::EVENT_TIME));
        $this->assertTrue($messageMarked->has(Header::EVENT_TYPE));
    }
}
