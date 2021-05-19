<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Exception\ReportFailed;
use Chronhub\Foundation\Exception\ValidationMessageFailed;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Content;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\PreValidationMessage;
use Chronhub\Foundation\Support\Contracts\Message\ValidationMessage;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Illuminate\Contracts\Validation\Factory;

final class ValidateCommand implements MessageSubscriber
{
    public function __construct(private Factory $validator)
    {
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $message = $context->message();

            if ( ! $message->isMessaging()) {
                return;
            }

            $this->validateEventIfRequired($message);
        }, Reporter::PRIORITY_MESSAGE_VALIDATION);
    }

    private function validateEventIfRequired(Message $message): void
    {
        $event = $message->event();

        if ( ! $event instanceof ValidationMessage) {
            return;
        }

        $alreadyProducedAsync = $event->header(Header::ASYNC_MARKER);

        if (null === $alreadyProducedAsync) {
            throw ReportFailed::missingAsyncMarkerHeader($message->header(Header::EVENT_TYPE));
        }

        if ($event instanceof PreValidationMessage && $alreadyProducedAsync) {
            return;
        }

        if ($event instanceof PreValidationMessage && ! $alreadyProducedAsync) {
            $this->validateMessage($message);
        }

        if ($alreadyProducedAsync) {
            $this->validateMessage($message);
        }
    }

    private function validateMessage(Message $message): void
    {
        /** @var ValidationMessage|Content $event */
        $event = $message->event();

        $validator = $this->validator->make($event->toContent(), $event->validationRules());

        if ($validator->fails()) {
            throw ValidationMessageFailed::withValidator($validator, $message);
        }
    }
}
