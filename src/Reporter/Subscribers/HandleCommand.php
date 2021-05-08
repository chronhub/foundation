<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;

final class HandleCommand implements MessageSubscriber
{
    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            if ($messageHandler = $context->messageHandlers()->current()) {
                $messageHandler($context->message()->event());

                $context->markMessageHandled(true);
            }
        }, Reporter::PRIORITY_INVOKE_HANDLER);
    }
}
