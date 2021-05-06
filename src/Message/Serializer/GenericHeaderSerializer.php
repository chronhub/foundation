<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Serializer;

use Chronhub\Foundation\Aggregate\AggregateChanged;
use Chronhub\Foundation\Message\Headers\AggregateIdHeader;
use Chronhub\Foundation\Message\Headers\AggregateIdTypeHeader;
use Chronhub\Foundation\Message\Headers\EventTypeHeader;
use Chronhub\Foundation\Message\Headers\Headers;
use Chronhub\Foundation\Message\Headers\IdentityHeader;
use Chronhub\Foundation\Message\Headers\InternalPositionHeader;
use Chronhub\Foundation\Message\Headers\TimeOfRecordingHeader;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\HeadingId;

final class GenericHeaderSerializer
{
    public function unserialize(string $source, array $payload): Headers
    {
        $headers = $payload['headers'];

        $headers[Header::EVENT_ID] = $this->normalizeEventId($headers[Header::EVENT_ID] ?? null);

        $headers[Header::EVENT_TYPE] = new EventTypeHeader($headers[Header::EVENT_TYPE]);

        $headers[Header::EVENT_TIME] = TimeOfRecordingHeader::fromString($headers[Header::EVENT_TIME]);

        if (is_subclass_of($source, AggregateChanged::class)) {
            if (!isset($headers[Header::INTERNAL_POSITION])) {
                $headers[Header::INTERNAL_POSITION] = new InternalPositionHeader(
                    $headers[Header::INTERNAL_POSITION]
                );
            }

            $headers[Header::AGGREGATE_ID] = AggregateIdHeader::fromType(
                $headers[Header::AGGREGATE_ID],
                $headers[Header::AGGREGATE_ID_TYPE],
            );

            $headers[Header::AGGREGATE_ID_TYPE] = new AggregateIdTypeHeader(
                $headers[Header::AGGREGATE_ID_TYPE],
            );
        }

        return new Headers(...$headers);
    }

    private function normalizeEventId(?string $eventId): HeadingId
    {
        if (null === $eventId) {
            return IdentityHeader::create();
        }

        return IdentityHeader::fromString($eventId);
    }
}
