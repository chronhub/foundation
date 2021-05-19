<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Exception\UnauthorizedException;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Reporter\AuthorizeMessage;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Tracker\ContextualMessage;

final class GuardCommandRoute implements MessageSubscriber
{
    public function __construct(private AuthorizeMessage $authorizationService,
                                private MessageAlias $messageAlias)
    {
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $message = $context->message();

            $eventAlias = $this->messageAlias->classToAlias($message->header(Header::EVENT_TYPE));

            if ($this->authorizationService->isNotGranted($eventAlias, $message)) {
                $context->stopPropagation(true);

                throw new UnauthorizedException("Unauthorized for event $eventAlias");
            }
        }, Reporter::PRIORITY_ROUTE + 1000);
    }
}
