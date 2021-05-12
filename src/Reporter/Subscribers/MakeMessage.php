<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;

final class MakeMessage implements MessageSubscriber
{
    public function __construct(private MessageFactory $factory)
    {
        //
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $message = $this->factory->createFromMessage($context->pullTransientMessage());

            $context->withMessage($message);
        }, Reporter::PRIORITY_MESSAGE_FACTORY);
    }
}
