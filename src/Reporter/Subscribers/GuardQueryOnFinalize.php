<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use React\Promise\PromiseInterface;

final class GuardQueryOnFinalize extends AbstractGuardQuery
{
    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::FINALIZE_EVENT, function (ContextualMessage $context): void {
            $promise = $context->promise();

            if ($promise instanceof PromiseInterface) {
                $promiseGuard = $promise->then(function ($result) use ($context) {
                    $this->authorizeQuery($context, $result);

                    return $result;
                });

                $context->withPromise($promiseGuard);
            }
        }, -1000);
    }
}
