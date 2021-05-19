<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;

final class NameReporterService implements MessageSubscriber
{
    public function __construct(private string $name)
    {
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $message = $context->message();

            if ($message->hasNot(Header::REPORTER_NAME)) {
                $context->withMessage(
                    $message->withHeader(Header::REPORTER_NAME, $this->name)
                );
            }
        }, Reporter::PRIORITY_MESSAGE_FACTORY - 1);
    }
}
