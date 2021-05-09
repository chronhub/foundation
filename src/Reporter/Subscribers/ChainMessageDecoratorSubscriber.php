<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;

final class ChainMessageDecoratorSubscriber implements MessageSubscriber
{
    public function __construct(private MessageDecorator $messageDecorator)
    {
        //
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $context->withMessage(
                $this->messageDecorator->decorate($context->message())
            );
        }, Reporter::PRIORITY_MESSAGE_DECORATOR);
    }
}
