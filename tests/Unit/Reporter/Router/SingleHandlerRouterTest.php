<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Router;

use Chronhub\Foundation\Exception\ReportFailed;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\Router\SingleHandlerRouter;
use Chronhub\Foundation\Support\Contracts\Reporter\Router;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Generator;
use Prophecy\Prophecy\ObjectProphecy;
use stdclass;

final class SingleHandlerRouterTest extends TestCaseWithProphecy
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
    public function it_route_message_to_one_handler_only(): void
    {
        $message = new Message(new stdclass());

        $expectedMessageHandlers = [function(){}];

        $this->router->route($message)->willReturn($expectedMessageHandlers)->shouldBeCalled();

        $router = new SingleHandlerRouter($this->router->reveal());

        $messageHandlers = $router->route($message);

        $this->assertEquals($expectedMessageHandlers, $messageHandlers);
    }

    /**
     * @test
     * @dataProvider provideInvalidCountMessageHandlers
     */
    public function it_raise_exception_with_invalid_count_message_handlers(array $messageHandlers): void
    {
        $this->expectException(ReportFailed::class);
        $this->expectExceptionMessage('Router require one message handler only');

        $message = new Message(new stdclass());

        $this->router->route($message)->willReturn($messageHandlers)->shouldBeCalled();

        $router = new SingleHandlerRouter($this->router->reveal());
        $router->route($message);
    }

    public function provideInvalidCountMessageHandlers(): Generator
    {
        yield [[]];

        yield [[
            function(){},
            function(){},
        ]];
    }
}
