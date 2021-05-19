<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Support\Contracts\Reporter;

use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use React\Promise\PromiseInterface;

interface Reporter
{
    public const DISPATCH_EVENT = 'dispatch_event';
    public const FINALIZE_EVENT = 'finalize_event';

    public const PRIORITY_MESSAGE_FACTORY = 100000;
    public const PRIORITY_MESSAGE_DECORATOR = 90000;
    public const PRIORITY_MESSAGE_VALIDATION = 30000;
    public const PRIORITY_ROUTE = 20000;
    public const PRIORITY_INVOKE_HANDLER = 0;

    /**
     * @param object|array $message
     *
     * @return void|PromiseInterface
     */
    public function publish(object|array $message);

    /**
     * @param MessageSubscriber ...$messageSubscribers
     */
    public function subscribe(MessageSubscriber ...$messageSubscribers): void;

    public function name(): string;
}
