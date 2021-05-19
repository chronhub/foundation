<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Reporter\Router;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Support\Contracts\Message\MessageProducer;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;

final class HandleRouter implements MessageSubscriber
{
    public function __construct(private Router $router,
                                private MessageProducer $messageProducer)
    {
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $this->messageProducer->isSync($context->message())
                ? $this->handleSyncMessage($context)
                : $this->handleAsyncMessage($context);
        }, Reporter::PRIORITY_ROUTE);
    }

    private function handleSyncMessage(ContextualMessage $context): void
    {
        $context->withMessageHandlers(
            $this->router->route($context->message())
        );
    }

    private function handleAsyncMessage(ContextualMessage $context): void
    {
        $asyncMessage = $this->messageProducer->produce($context->message());

        $context->withMessage($asyncMessage);
    }
}
