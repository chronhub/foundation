<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Subscribers;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Support\Contracts\Tracker\ContextualMessage;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageSubscriber;
use Chronhub\Foundation\Support\Contracts\Tracker\MessageTracker;
use Psr\Log\LoggerInterface;
use function is_array;
use function serialize;

final class LogDomainCommand implements MessageSubscriber
{
    public function __construct(private LoggerInterface $logger,
                                private MessageSerializer $messageSerializer)
    {
    }

    public function attachToTracker(MessageTracker $tracker): void
    {
        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $message = $context->transientMessage();

            if ( ! is_array($message)) {
                return;
            }

            $this->logger->debug('On dispatch to factory array command', [
                'context' => [
                    'message_name' => $this->determineMessageName($message),
                    'message'      => $message,
                ],
            ]);
        }, Reporter::PRIORITY_MESSAGE_FACTORY + 1);

        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $message = $context->message();

            $serializedMessage = $message->isMessaging()
                ? $this->messageSerializer->serializeMessage($message)
                : serialize($message->event());

            $this->logger->debug('On dispatch to route', [
                'context' => [
                    'message_name' => $this->determineMessageName($message),
                    'exception'    => $context->exception(),
                    'message'      => $serializedMessage,
                ],
            ]);
        }, Reporter::PRIORITY_ROUTE - 1);

        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $this->logger->debug('On dispatch after route', [
                'context' => [
                    'message_name' => $this->determineMessageName($context->message()),
                    'async_marker' => $context->message()->header(Header::ASYNC_MARKER),
                    'exception'    => $context->exception(),
                ],
            ]);
        }, Reporter::PRIORITY_ROUTE - 1);

        $tracker->listen(Reporter::DISPATCH_EVENT, function (ContextualMessage $context): void {
            $message = $context->message();

            $serializedMessage = $message->isMessaging()
                ? $this->messageSerializer->serializeMessage($message)
                : serialize($message->event());

            $this->logger->debug('On dispatch before invoke message handler', [
                'context' => [
                    'message_name'         => $this->determineMessageName($message),
                    'has_message_handlers' => iterator_count($context->messageHandlers()) > 0,
                    'exception'            => $context->exception(),
                    'message'              => $serializedMessage,
                ],
            ]);
        }, Reporter::PRIORITY_INVOKE_HANDLER + 1);

        $tracker->listen(Reporter::FINALIZE_EVENT, function (ContextualMessage $context): void {
            $this->logger->debug('On pre finalize message command', [
                'context' => [
                    'message_name'    => $this->determineMessageName($context->message()),
                    'message_handled' => $context->isMessageHandled(),
                    'async_marker' => $context->message()->header(Header::ASYNC_MARKER),
                    'exception'       => $context->exception(),
                ],
            ]);
        }, 100000);

        $tracker->listen(Reporter::FINALIZE_EVENT, function (ContextualMessage $context): void {
            $this->logger->debug('On post finalize message command', [
                'context' => [
                    'message_name'    => $this->determineMessageName($context->message()),
                    'message_handled' => $context->isMessageHandled(),
                    'async_marker' => $context->message()->header(Header::ASYNC_MARKER),
                    'exception'       => $context->exception(),
                ],
            ]);
        }, -100000);
    }

    private function determineMessageName(Message|array $message): string
    {
        if ($message instanceof Message) {
            $eventType = $message->header(Header::EVENT_TYPE);

            return $eventType ?? $message->event()::class;
        }

        return $message['headers'][Header::EVENT_TYPE] ?? $message['message_name'] ?? 'Undetermined event type';
    }
}
