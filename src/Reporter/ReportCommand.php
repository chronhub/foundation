<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter;

use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Throwable;

class ReportCommand implements Reporter
{
    use HasReporter;

    private array $queue = [];
    private bool $isDispatching = false;

    public function publish(object|array $message)
    {
        $this->queue[] = $message;

        if (!$this->isDispatching) {
            $this->isDispatching = true;

            try {
                while ($command = array_shift($this->queue)) {
                    $context = $this->tracker->newContext(self::DISPATCH_EVENT);

                    $context->withTransientMessage($command);

                    $this->publishMessage($context);
                }

                $this->isDispatching = false;
            } catch (Throwable $exception) {
                $this->isDispatching = false;

                throw $exception;
            }
        }
    }
}
