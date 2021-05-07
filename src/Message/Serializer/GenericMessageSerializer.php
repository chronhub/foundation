<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Serializer;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Message\Payload;
use Chronhub\Foundation\Support\Contracts\Message\Content;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;
use Generator;
use RuntimeException;

final class GenericMessageSerializer implements MessageSerializer
{
    private GenericContentSerializer $contentSerializer;
    private GenericHeaderSerializer $headerSerializer;

    public function __construct(?GenericContentSerializer $contentSerializer = null,
                                ?GenericHeaderSerializer $headerSerializer = null)
    {
        $this->contentSerializer = $contentSerializer ?? new GenericContentSerializer();
        $this->headerSerializer = $headerSerializer ?? new GenericHeaderSerializer();
    }

    public function serializeMessage(Message $message): Payload
    {
        $event = $message->event();

        if (!$event instanceof Content) {
            throw new RuntimeException("Message event must be an instance of Content to be serialized");
        }

        return new Payload(
            $message->headers()->jsonSerialize(),
            $this->contentSerializer->serialize($event),
            null
        );
    }

    public function unserializeContent(array $payload): Generator
    {
        $source = $payload['headers'][Header::EVENT_TYPE] ?? null;

        if (null === $source) {
            throw new InvalidArgumentException("Missing event type header from payload");
        }

        $event = $this->contentSerializer->unserialize($source, $payload);

        $headers = $this->headerSerializer->unserialize($source, $payload);

        yield $event->withHeaders($headers);

        //yield new Message($event, ...$headers->toArray());
    }
}
