<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Exception\CollectedExceptionMessage;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Throwable;

final class HandleSequentialEvent implements MessageSubscriber
{
    public function __construct(private bool $raiseCollectedExceptions)
    {
        //
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $exceptions = [];

            $messageHandled = false;

            foreach ($context->messageHandlers() as $messageHandler) {
                try {
                    $messageHandler($context->message()->event());

                    $messageHandled = true;
                } catch (Throwable $exception) {
                    $exceptions[] = $exception;
                }
            }

            $context->markMessageHandled($messageHandled);

            if (count($exceptions) > 0) {
                $collectedExceptions = CollectedExceptionMessage::fromExceptions(...$exceptions);

                if ($this->raiseCollectedExceptions) {
                    throw $collectedExceptions;
                }

                $context->withRaisedException($collectedExceptions);
            }

        }, Reporter::PRIORITY_INVOKE_HANDLER);
    }
}
