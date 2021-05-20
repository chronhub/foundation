<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Router;

use stdclass;
use Prophecy\Prophecy\ObjectProphecy;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Chronhub\Foundation\Support\Contracts\Reporter\Router;
use Chronhub\Foundation\Reporter\Router\DomainEventHandlerRouter;

/** @coversDefaultClass \Chronhub\Foundation\Reporter\Router\DomainEventHandlerRouter */
final class DomainEventHandlerRouterTest extends TestCaseWithProphecy
{
    private ObjectProphecy|Router $router;

    public function setUp(): void
    {
        parent::setUp();

        $this->router = $this->prophesize(Router::class);
    }

    /**
     * @test
     */
    public function it_route_message_to_multiple_message_handlers(): void
    {
        $message = new Message(new stdclass());

        $expectedMessageHandlers = [
            function (): void {},
            function (): void {},
        ];

        $this->router->route($message)->willReturn($expectedMessageHandlers)->shouldBeCalled();

        $router = new DomainEventHandlerRouter($this->router->reveal());

        $messageHandlers = $router->route($message);

        $this->assertEquals($expectedMessageHandlers, $messageHandlers);
    }

    /**
     * @test
     */
    public function it_route_message_to_empty_message_handlers(): void
    {
        $message = new Message(new stdclass());

        $expectedMessageHandlers = [];

        $this->router->route($message)->willReturn($expectedMessageHandlers)->shouldBeCalled();

        $router = new DomainEventHandlerRouter($this->router->reveal());

        $messageHandlers = $router->route($message);

        $this->assertEquals($expectedMessageHandlers, $messageHandlers);
    }
}
