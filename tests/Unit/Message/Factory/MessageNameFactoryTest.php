<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Factory;

use stdClass;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\Double\SomeCommand;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Message\Factory\MessageNameFactory;
use Chronhub\Foundation\Message\Factory\GenericMessageFactory;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;

/** @coversDefaultClass \Chronhub\Foundation\Message\Factory\MessageNameFactory */
final class MessageNameFactoryTest extends TestCaseWithProphecy
{
    /**
     * @test
     */
    public function it_normalize_message_as_array(): void
    {
        $fakeMessage = new Message(new stdClass());

        $serializer = $this->prophesize(MessageSerializer::class);
        $serializer->unserializeContent(
            [
                'headers' => [Header::EVENT_ID => '123', Header::EVENT_TYPE => SomeCommand::class],
                'content' => ['name' => 'steph'],
            ]
        )->willYield([$fakeMessage])->shouldBeCalled();

        $genericFactory = new GenericMessageFactory($serializer->reveal());

        $command = [
            'headers'      => [Header::EVENT_ID => '123'],
            'message_name' => SomeCommand::class,
            'content'      => ['name' => 'steph'],
        ];

        $factory = new MessageNameFactory($genericFactory);
        $message = $factory->createFromMessage($command);

        $this->assertEquals($fakeMessage, $message);
    }

    /**
     * @test
     */
    public function it_provider_header_and_content_array_key_if_it_does_not_exists(): void
    {
        $fakeMessage = new Message(new stdClass());

        $serializer = $this->prophesize(MessageSerializer::class);
        $serializer->unserializeContent(
            [
                'headers' => [Header::EVENT_TYPE => SomeCommand::class],
                'content' => [],
            ]
        )->willYield([$fakeMessage])->shouldBeCalled();

        $genericFactory = new GenericMessageFactory($serializer->reveal());

        $command = ['message_name' => SomeCommand::class];

        $factory = new MessageNameFactory($genericFactory);
        $message = $factory->createFromMessage($command);

        $this->assertEquals($fakeMessage, $message);
    }

    /**
     * @test
     */
    public function it_raise_exception_if_message_is_not_array(): void
    {
        $this->expectExceptionMessage('Message name factory instance can handle array event only');

        $serializer = $this->prophesize(MessageSerializer::class);
        $genericFactory = new GenericMessageFactory($serializer->reveal());
        $factory = new MessageNameFactory($genericFactory);

        $factory->createFromMessage(new stdClass());
    }

    /**
     * @test
     */
    public function it_raise_exception_if_message_name_array_key_is_missing(): void
    {
        $this->expectExceptionMessage('Missing message name key from array payload');

        $serializer = $this->prophesize(MessageSerializer::class);
        $genericFactory = new GenericMessageFactory($serializer->reveal());
        $factory = new MessageNameFactory($genericFactory);

        $factory->createFromMessage([]);
    }

    /**
     * @test
     */
    public function it_raise_exception_if_message_name_is_not_a_valid_class_name(): void
    {
        $this->expectExceptionMessage('Message name must be a fqcn');

        $serializer = $this->prophesize(MessageSerializer::class);
        $genericFactory = new GenericMessageFactory($serializer->reveal());
        $factory = new MessageNameFactory($genericFactory);

        $factory->createFromMessage(['message_name' => 'invalid_class']);
    }
}
