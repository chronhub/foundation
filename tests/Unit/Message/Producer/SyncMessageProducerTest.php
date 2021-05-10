<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Producer;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Message\Producer\SyncMessageProducer;
use Chronhub\Foundation\Tests\TestCase;
use stdClass;

final class SyncMessageProducerTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_true_on_is_async_method(): void
    {
        $message = new Message(new stdClass());

        $producer = new SyncMessageProducer();

        $this->assertTrue($producer->isSync($message));
    }

    /**
     * @test
     */
    public function it_return_message(): void
    {
        $message = new Message(new stdClass());

        $producer = new SyncMessageProducer();

        $this->assertEquals($message, $producer->produce($message));
    }
}
