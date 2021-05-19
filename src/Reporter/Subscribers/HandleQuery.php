<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use React\Promise\Deferred;
use Throwable;

final class HandleQuery implements MessageSubscriber
{
    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            if ($messageHandler = $context->messageHandlers()->current()) {
                $event = $context->message()->event();

                $deferred = new Deferred();

                try {
                    $messageHandler($event, $deferred);
                } catch (Throwable $exception) {
                    $deferred->reject($exception);
                } finally {
                    $context->withPromise($deferred->promise());
                    $context->markMessageHandled(true);
                }
            }
        }, Reporter::PRIORITY_INVOKE_HANDLER);
    }
}
