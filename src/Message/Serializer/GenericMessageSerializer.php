<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Serializer;

use Generator;
use Ramsey\Uuid\Uuid;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Attribute\Payload;
use Chronhub\Foundation\Aggregate\AggregateChanged;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Support\Contracts\Clock\Clock;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\Content;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use function get_class;
use function is_string;

final class GenericMessageSerializer implements MessageSerializer
{
    private GenericContentSerializer $contentSerializer;

    public function __construct(private Clock $clock,
                                ?GenericContentSerializer $contentSerializer = null)
    {
        $this->contentSerializer = $contentSerializer ?? new GenericContentSerializer();
    }

    #[Payload(['headers' => 'array', 'content' => 'array'])]
    public function serializeMessage(Message $message): array
    {
        $event = $message->event();

        if ( ! $event instanceof Content) {
            throw new RuntimeException('Message event must be an instance of Content to be serialized');
        }

        $headers = $message->headers();
        $headers = $this->normalizeEventId($headers);
        $headers = $this->normalizeEventTime($headers);

        if ( ! isset($headers[Header::EVENT_TYPE])) {
            $headers[Header::EVENT_TYPE] = get_class($event);
        }

        if (is_subclass_of($event, AggregateChanged::class)) {
            $headers = $this->normalizeAggregateIdAndType($headers);
        }

        return [
            'headers' => $headers,
            'content' => $this->contentSerializer->serialize($event),
        ];
    }

    #[Payload]
    public function unserializeContent(array $payload): Generator
    {
        $headers = $payload['headers'];

        $source = $headers[Header::EVENT_TYPE] ?? null;

        if (null === $source) {
            throw new RuntimeException('Missing event type header from payload');
        }

        $event = $this->contentSerializer->unserialize($source, $payload);

        $headers = $this->normalizeEventId($headers);
        $headers = $this->normalizeEventTime($headers);

        if (is_subclass_of($source, AggregateChanged::class)) {
            if ( ! isset($headers[Header::INTERNAL_POSITION])) {
                $headers[Header::INTERNAL_POSITION] = $payload['no'];
            }

            $headers = $this->normalizeAggregateIdAndType($headers);
        }

        yield $event->withHeaders($headers);
    }

    private function normalizeEventId(array $headers): array
    {
        $eventId = $headers[Header::EVENT_ID] ?? null;

        if (null === $eventId) {
            return $headers + [Header::EVENT_ID => Uuid::uuid4()->toString()];
        }

        if ( ! is_string($eventId)) {
            $headers[Header::EVENT_ID] = (string) $headers[Header::EVENT_ID];
        }

        return $headers;
    }

    private function normalizeEventTime(array $headers): array
    {
        $eventTime = $headers[Header::EVENT_TIME] ?? null;

        if (null === $eventTime) {
            return $headers + [Header::EVENT_TIME => $this->clock->fromNow()->toString()];
        }

        if ( ! is_string($eventTime)) {
            $headers[Header::EVENT_TIME] = (string) $headers[Header::EVENT_TIME];
        }

        return $headers;
    }

    private function normalizeAggregateIdAndType(array $headers): array
    {
        if ( ! isset($headers[Header::AGGREGATE_ID], $headers[Header::AGGREGATE_ID_TYPE])) {
            throw new RuntimeException('Missing aggregate id and type');
        }

        return $headers;
    }
}
