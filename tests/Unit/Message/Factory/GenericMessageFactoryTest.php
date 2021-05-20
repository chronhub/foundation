<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Factory;

use stdClass;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Message\Factory\GenericMessageFactory;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;

/** @coversDefaultClass \Chronhub\Foundation\Message\Factory\GenericMessageFactory */
final class GenericMessageFactoryTest extends TestCaseWithProphecy
{
    /**
     * @test
     *
     * @covers ::createFromMessage
     */
    public function it_create_message_from_array(): void
    {
        $expectedMessage = new Message(new stdClass());

        $serializer = $this->prophesize(MessageSerializer::class);
        $serializer
            ->unserializeContent(['foo' => 'bar'])
            ->willYield([$expectedMessage])
            ->shouldBeCalled();

        $factory = new GenericMessageFactory($serializer->reveal());

        $message = $factory->createFromMessage(['foo' => 'bar']);

        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * @test
     */
    public function it_create_message_from_message_instance(): void
    {
        $expectedMessage = new Message(new stdClass());

        $serializer = $this->prophesize(MessageSerializer::class)->reveal();
        $factory = new GenericMessageFactory($serializer);

        $message = $factory->createFromMessage($expectedMessage);

        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * @test
     */
    public function it_create_message_from_event_instance(): void
    {
        $expectedEvent = SomeCommand::fromContent(['name' => 'steph']);

        $serializer = $this->prophesize(MessageSerializer::class)->reveal();
        $factory = new GenericMessageFactory($serializer);

        $message = $factory->createFromMessage($expectedEvent);

        $this->assertEquals($expectedEvent, $message->event());
    }

    /**
     * @test
     */
    public function it_create_message_from_event_instance_with_headers(): void
    {
        $expectedEvent = SomeCommand::fromContent(['name' => 'steph']);
        $expectedEvent = $expectedEvent->withHeader('some', 'header');

        $serializer = $this->prophesize(MessageSerializer::class)->reveal();
        $factory = new GenericMessageFactory($serializer);

        $message = $factory->createFromMessage($expectedEvent);

        $this->assertEquals($expectedEvent->headers(), $message->event()->headers());
    }
}
