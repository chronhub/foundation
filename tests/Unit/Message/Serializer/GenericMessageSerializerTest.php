<?php

namespace Chronhub\Foundation\Tests\Unit\Message\Serializer;

use Chronhub\Foundation\Aggregate\GenericAggregateId;
use Chronhub\Foundation\Clock\UniversalPointInTime;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Message\Payload;
use Chronhub\Foundation\Message\Serializer\GenericMessageSerializer;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\Double\SomeAggregateChanged;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use stdClass;
use function get_class;

class GenericMessageSerializerTest extends TestCaseWithProphecy
{
    private ObjectProphecy|Clock $clock;

    public function setUp(): void
    {
        parent::setUp();

        $this->clock = $this->prophesize(Clock::class);
    }

    /**
     * @test
     */
    public function it_serialize_message(): void
    {
        $event = SomeCommand::fromContent(['name' => 'steph']);
        $headers = [
            Header::EVENT_TYPE => $eventClass = SomeCommand::class,
            Header::EVENT_ID   => $id = Uuid::uuid4(),
            Header::EVENT_TIME => $time = UniversalPointInTime::now(),
        ];

        $message = new Message($event, $headers);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $serializedEvent = $serializer->serializeMessage($message);

        $payload = new Payload(
            [
                Header::EVENT_TYPE => $eventClass,
                Header::EVENT_ID   => $id->toString(),
                Header::EVENT_TIME => $time->toString(),
            ],
            ['name' => 'steph'],
            null
        );

        $this->assertEquals($payload, $serializedEvent);
    }

    /**
     * @test
     */
    public function it_serialize_message_and_provide_missing_default_headers(): void
    {
        $pointInTime = $this->prophesize(PointInTime::class);
        $pointInTime->toString()->willReturn('some_date_time')->shouldBeCalled();
        $this->clock->fromNow()->willReturn($pointInTime)->shouldBeCalled();

        $event = SomeCommand::fromContent(['name' => 'steph']);

        $message = new Message($event, []);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $serializedEvent = $serializer->serializeMessage($message);

        $this->assertIsString($serializedEvent->headers()[Header::EVENT_ID]);
        $this->assertEquals(SomeCommand::class, $serializedEvent->headers()[Header::EVENT_TYPE]);
        $this->assertEquals('some_date_time', $serializedEvent->headers()[Header::EVENT_TIME]);
    }

    /**
     * @test
     */
    public function it_serialize_aggregate_id_if_event_subclass_of_aggregate_changed(): void
    {
        $pointInTime = $this->prophesize(PointInTime::class);
        $pointInTime->toString()->willReturn('some_date_time')->shouldBeCalled();
        $this->clock->fromNow()->willReturn($pointInTime)->shouldBeCalled();

        $aggregateId = GenericAggregateId::create();

        $event = SomeAggregateChanged::occur($aggregateId->toString(), ['name' => 'steph']);
        $headers = [
            Header::AGGREGATE_ID => $aggregateId,
        ];

        $message = new Message($event, $headers);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);
        $serializedEvent = $serializer->serializeMessage($message);

        $this->assertEquals($serializedEvent->headers()[Header::AGGREGATE_ID], $aggregateId->toString());
    }

    /**
     * @test
     */
    public function it_raise_exception_if_event_not_instance_of_content_interface_on_serialization(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Message event must be an instance of Content to be serialized');

        $message = new Message(new stdclass(), []);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $serializer->serializeMessage($message);
    }

    /**
     * @test
     */
    public function it_raise_exception_with_string_aggregate_id_header_and_missing_aggregate_id_type(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing aggregate id type');

        $pointInTime = $this->prophesize(PointInTime::class);
        $pointInTime->toString()->willReturn('some_date_time')->shouldBeCalled();
        $this->clock->fromNow()->willReturn($pointInTime)->shouldBeCalled();

        $aggregateId = GenericAggregateId::create();

        $event = SomeAggregateChanged::occur($aggregateId->toString(), ['name' => 'steph']);
        $headers = [Header::AGGREGATE_ID => $aggregateId->toString(),];

        $message = new Message($event, $headers);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);
        $serializer->serializeMessage($message);
    }

    /**
     * @test
     */
    public function it_raise_exception_if_event_id_is_not_instance_of_uuid_interface(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid event id header');

        $event = SomeCommand::fromContent(['name' => 'steph']);
        $headers = [Header::EVENT_ID => new stdClass()];

        $message = new Message($event, $headers);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $serializer->serializeMessage($message);
    }

    /**
     * @test
     */
    public function it_raise_exception_if_event_time_is_not_instance_of_point_in_time(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid event time header');

        $event = SomeCommand::fromContent(['name' => 'steph']);
        $headers = [
            Header::EVENT_TIME => new stdClass()
        ];

        $message = new Message($event, $headers);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $serializer->serializeMessage($message);
    }

    /**
     * @test
     */
    public function it_unserialize_payload(): void
    {
        $id = Uuid::uuid4();
        $time = UniversalPointInTime::now();
        $eventClass = SomeCommand::class;

        $pointInTime = $this->prophesize(PointInTime::class)->reveal();
        $this->clock->fromString($time->toString())->willReturn($pointInTime)->shouldBeCalled();

        $headers = [
            Header::EVENT_TYPE => $eventClass,
            Header::EVENT_ID   => $id->toString(),
            Header::EVENT_TIME => $time->toString(),
        ];

        $content = ['name' => 'steph'];

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $event = $serializer->unserializeContent([
            'headers' => $headers,
            'content' => $content,
        ])->current();


        $this->assertEquals($eventClass, $event::class);
        $this->assertEquals($content, $event->toContent());

        $this->assertInstanceOf(UuidInterface::class, $event->header(Header::EVENT_ID));
        $this->assertEquals($id, $event->header(Header::EVENT_ID));

        $this->assertEquals($pointInTime, $event->header(Header::EVENT_TIME));
    }

    /**
     * @test
     */
    public function it_unserialize_payload_from_aggregate_changed_source(): void
    {
        $aggregateId = GenericAggregateId::create();

        $id = Uuid::uuid4();
        $time = UniversalPointInTime::now();
        $eventClass = SomeAggregateChanged::class;

        $pointInTime = $this->prophesize(PointInTime::class)->reveal();
        $this->clock->fromString($time->toString())->willReturn($pointInTime)->shouldBeCalled();

        $headers = [
            Header::AGGREGATE_ID      => $aggregateId->toString(),
            Header::AGGREGATE_ID_TYPE => get_class($aggregateId),
            Header::INTERNAL_POSITION => 1,
            Header::EVENT_TYPE        => $eventClass,
            Header::EVENT_ID          => $id->toString(),
            Header::EVENT_TIME        => $time->toString(),
        ];

        $content = ['name' => 'steph'];

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $event = $serializer->unserializeContent([
            'headers' => $headers,
            'content' => $content,
        ])->current();


        $this->assertEquals($eventClass, $event::class);
        $this->assertEquals($content, $event->toContent());
        $this->assertInstanceOf(UuidInterface::class, $event->header(Header::EVENT_ID));
        $this->assertEquals($id, $event->header(Header::EVENT_ID));
        $this->assertEquals($pointInTime, $event->header(Header::EVENT_TIME));

        $this->assertInstanceOf(AggregateId::class, $event->header(Header::AGGREGATE_ID));
        $this->assertEquals($aggregateId, $event->header(Header::AGGREGATE_ID));
        $this->assertEquals(1, $event->header(Header::INTERNAL_POSITION));
    }

    /**
     * @test
     */
    public function it_add_internal_version_header_on_unserializing_aggregate_changed(): void
    {
        $aggregateId = GenericAggregateId::create();

        $id = Uuid::uuid4();
        $time = UniversalPointInTime::now();
        $eventClass = SomeAggregateChanged::class;

        $pointInTime = $this->prophesize(PointInTime::class)->reveal();
        $this->clock->fromString($time->toString())->willReturn($pointInTime)->shouldBeCalled();

        $headers = [
            Header::AGGREGATE_ID      => $aggregateId->toString(),
            Header::AGGREGATE_ID_TYPE => get_class($aggregateId),
            Header::EVENT_TYPE        => $eventClass,
            Header::EVENT_ID          => $id->toString(),
            Header::EVENT_TIME        => $time->toString()
        ];

        $content = ['name' => 'steph'];

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $event = $serializer->unserializeContent([
            'headers' => $headers,
            'content' => $content,
            'no'      => 1
        ])->current();

        $this->assertEquals(1, $event->header(Header::INTERNAL_POSITION));
    }

    /**
     * @test
     */
    public function it_raise_exception_with_missing_event_type_header_on_unserializing_event(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing event type header from payload');

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);

        $serializer->unserializeContent(['headers' => []])->current();
    }

    /**
     * @test
     */
    public function it_raise_exception_with_invalid_aggregate_id_header_instance_on_serialization(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid aggregate id');

        $pointInTime = $this->prophesize(PointInTime::class);
        $pointInTime->toString()->willReturn('some_date_time')->shouldBeCalled();
        $this->clock->fromNow()->willReturn($pointInTime)->shouldBeCalled();

        $event = SomeAggregateChanged::occur(GenericAggregateId::create()->toString(), ['name' => 'steph']);
        $headers = [
            Header::AGGREGATE_ID => new stdClass(),
            Header::AGGREGATE_ID_TYPE => GenericAggregateId::class,
        ];

        $message = new Message($event, $headers);

        $serializer = new GenericMessageSerializer($this->clock->reveal(), null);
        $serializer->serializeMessage($message);
    }
}
