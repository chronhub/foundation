<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use React\Promise\PromiseInterface;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;

final class GuardQueryOnDispatch extends AbstractGuardQuery
{
    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $promise = $context->promise();

            if ($promise instanceof PromiseInterface) {
                $this->authorizeQuery($context);
            }
        }, Reporter::PRIORITY_INVOKE_HANDLER - 1);
    }
}
