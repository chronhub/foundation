<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter;

use Chronhub\Foundation\Exception\MessageDispatchFailed;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Chronhub\Foundation\Tracker\TrackMessage;
use Throwable;
use function get_called_class;

trait HasReporter
{
    private MessageTracker $tracker;

    public function __construct(private ?string $name = null, ?MessageTracker $tracker = null)
    {
        $this->tracker = $tracker ?? new TrackMessage();
    }

    protected function publishMessage(ContextualMessage $context): void
    {
        try {
            $this->tracker->fire($context);

            if (!$context->isMessageHandled()) {
                $messageName = $context->message()->header(Header::EVENT_TYPE);

                throw new RuntimeException("Message $messageName was not handled");
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
