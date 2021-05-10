<?php

namespace Chronhub\Foundation\Tests\Unit\Message\Serializer;

use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Serializer\GenericContentSerializer;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\Double\SomeAggregateChanged;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCase;
use stdClass;

class GenericContentSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function it_serialize_event_content(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);

        $serializer = new GenericContentSerializer();

        $this->assertEquals(['name' => 'steph'], $serializer->serialize($event));
    }

    /**
     * @test
     */
    public function it_unserialize_content_from_event_content(): void
    {
        $payload = [
            'headers' => ['some' => 'header'],
            'content' => ['name' => 'steph']
        ];

        $serializer = new GenericContentSerializer();

        $event = $serializer->unserialize(SomeCommand::class, $payload);

        $this->assertInstanceOf(SomeCommand::class, $event);
        $this->assertEmpty($event->headers());
    }

    /**
     * @test
     */
    public function it_unserialize_content_from_aggregate_changed_event(): void
    {
        $payload = [
            'headers' => [Header::AGGREGATE_ID => '123-456'],
            'content' => ['name' => 'steph']
        ];

        $serializer = new GenericContentSerializer();

        $event = $serializer->unserialize(SomeAggregateChanged::class, $payload);

        $this->assertInstanceOf(SomeAggregateChanged::class, $event);
        $this->assertEmpty($event->headers());

        $this->assertEquals('123-456', $event->aggregateId());
    }

    /**
     * @test
     */
    public function it_raise_exception_when_unserialize_an_invalid_event_type(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid source');

        (new GenericContentSerializer())->unserialize(stdClass::class,['some' => 'content']);
    }
}
