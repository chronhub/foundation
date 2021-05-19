<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter;

use Throwable;
use Chronhub\Foundation\Tracker\TrackMessage;
use Chronhub\Foundation\Exception\MessageNotHandled;
use Chronhub\Foundation\Exception\MessageDispatchFailed;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use function get_called_class;

trait HasReporter
{
    protected MessageTracker $tracker;

    public function __construct(protected ?string $name = null, ?MessageTracker $tracker = null)
    {
        $this->tracker = $tracker ?? new TrackMessage();
    }

    protected function publishMessage(ContextualMessage $context): void
    {
        try {
            $this->tracker->fire($context);

            if ( ! $context->isMessageHandled()) {
                $messageName = $context->message()->header(Header::EVENT_TYPE);

                throw MessageNotHandled::withMessageName($messageName);
            }
        } catch (Throwable $exception) {
            $wrapException = MessageDispatchFailed::withException($exception);

            $context->withRaisedException($wrapException);
        } finally {
            $context->stopPropagation(false);

            $context->withEvent(self::FINALIZE_EVENT);

            $this->tracker->fire($context);

            if ($context->hasException()) {
                throw $context->exception();
            }
        }
    }

    public function subscribe(MessageSubscriber ...$subscribers): void
    {
        foreach ($subscribers as $subscriber) {
            $subscriber->attachToTracker($this->tracker);
        }
    }

    public function name(): string
    {
        return $this->name ?? get_called_class();
    }
}
