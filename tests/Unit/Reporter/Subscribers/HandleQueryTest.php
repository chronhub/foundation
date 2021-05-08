<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\Subscribers\HandleQuery;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Tests\Double\SomeQuery;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tracker\TrackMessage;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Throwable;

final class HandleQueryTest extends TestCase
{
    /**
     * @test
     */
    public function it_handle_query(): void
    {
        $message = new Message(SomeQuery::fromContent(['name' => 'steph']));

        $handled = false;
        $messageHandler = function (SomeQuery $query, Deferred $promise) use (&$handled): void {
            $handled = true;

            $promise->resolve($query->toContent());
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleQuery();
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$messageHandler]);

        $tracker->fire($context);

        $this->assertTrue($handled);
        $this->assertTrue($context->isMessageHandled());

        $promise = $context->promise();

        $this->assertInstanceOf(PromiseInterface::class, $promise);
        $this->assertEquals(['name' => 'steph'], $this->handlePromise($promise));
    }

    /**
     * @test
     */
    public function it_handle_first_query_handler_only(): void
    {
        $message = new Message(SomeQuery::fromContent(['name' => 'steph']));

        $handled = [false, false];
        $oneHandler = function (SomeQuery $query, Deferred $promise) use (&$handled): void {
            $handled[0] = true;

            $promise->resolve($query->toContent());
        };

        $secondHandler = function (SomeQuery $query, Deferred $promise) use (&$handled): void {
            $handled = true;

            $promise->resolve(['should not be called']);
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleQuery();
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$oneHandler, $secondHandler]);

        $tracker->fire($context);

        $this->assertEquals([true, false], $handled);
        $this->assertTrue($context->isMessageHandled());

        $promise = $context->promise();

        $this->assertInstanceOf(PromiseInterface::class, $promise);
        $this->assertEquals(['name' => 'steph'], $this->handlePromise($promise));
    }

    /**
     * @test
     */
    public function it_does_not_mark_message_handled_when_context_message_handlers_is_empty(): void
    {
        $message = new Message(SomeQuery::fromContent(['name' => 'steph']));

        $tracker = new TrackMessage();

        $subscriber = new HandleQuery();
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);

        $tracker->fire($context);

        $this->assertFalse($context->isMessageHandled());

        $this->assertNull($context->promise());
    }

    /**
     * @test
     */
    public function it_hold_raise_exception_on_promise(): void
    {
        $message = new Message(SomeQuery::fromContent(['name' => 'steph']));
        $exception = new RuntimeException('some_exception_message');

        $messageHandler = function () use ($exception): void {
            throw $exception;
        };

        $tracker = new TrackMessage();

        $subscriber = new HandleQuery();
        $subscriber->attachToTracker($tracker);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);
        $context->withMessageHandlers([$messageHandler]);

        $tracker->fire($context);

        $this->assertTrue($context->isMessageHandled());

        $promise = $context->promise();

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $result = $this->handlePromise($promise, false);

        $this->assertEquals($exception, $result);
    }

    private function handlePromise(PromiseInterface $promise, bool $raiseException = false): mixed
    {
        $exception = null;
        $result = null;

        $promise->then(
            static function ($data) use (&$result) {
                $result = $data;
            },
            static function ($exc) use (&$exception) {
                $exception = $exc;
            }
        );

        if ($exception instanceof Throwable) {
            if (!$raiseException) {
                return $exception;
            }

            throw $exception;
        }

        return $result;
    }
}