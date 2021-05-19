<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Exception\UnauthorizedException;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Chronhub\Foundation\Support\Contracts\Reporter\AuthorizeMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;

abstract class AbstractGuardQuery implements MessageSubscriber
{
    public function __construct(private AuthorizeMessage $authorizationService,
                                private MessageAlias $messageAlias)
    {
    }

    protected function authorizeQuery(ContextualMessage $context, mixed $result = null): void
    {
        $message = $context->message();

        $eventAlias = $this->messageAlias->classToAlias($message->header(Header::EVENT_TYPE));

        if ($this->authorizationService->isNotGranted($eventAlias, $message, $result)) {
            $context->stopPropagation(true);

            throw new UnauthorizedException("Unauthorized for event $eventAlias");
        }
    }
}
