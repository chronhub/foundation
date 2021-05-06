<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Factory;

use Chronhub\Foundation\Message\Factory\GenericMessageFactory;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use stdClass;

/** @coversDefaultClass \Chronhub\Foundation\Message\Factory\GenericMessageFactory */
final class GenericMessageFactoryTest extends TestCaseWithProphecy
{
    /**
     * @test
     *
     * @covers ::createFrom
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

        $message = $factory->createFrom(['foo' => 'bar']);

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

        $message = $factory->createFrom($expectedMessage);

        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * @test
     */
    public function it_create_message_from_event_instance(): void
    {
        $expectedEvent = new stdClass();

        $serializer = $this->prophesize(MessageSerializer::class)->reveal();
        $factory = new GenericMessageFactory($serializer);

        $message = $factory->createFrom($expectedEvent);

        $this->assertEquals($expectedEvent, $message->event());
    }
}
