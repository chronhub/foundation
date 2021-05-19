<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter;

use React\Promise\PromiseInterface;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;

class ReportQuery implements Reporter
{
    use HasReporter;

    public function publish(object|array $query): PromiseInterface
    {
        $context = $this->tracker->newContext(Reporter::DISPATCH_EVENT);

        $context->withTransientMessage($query);

        $this->publishMessage($context);

        $promise = $context->promise();

        if ( ! $promise && $context->hasException()) {
            throw $context->exception();
        }

        return $promise;
    }
}
