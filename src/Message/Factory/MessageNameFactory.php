<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Factory;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;
use Chronhub\Foundation\Support\Contracts\Message\MessageSerializer;

final class MessageNameFactory implements MessageFactory
{
    public function __construct(private MessageSerializer $serializer)
    {
        //
    }

    public function createFrom(object|array $event): Message
    {
        // have to define a specific target for reporter
        // in reporter config bus name when dispatching async
        // as it would fail coming back here as default array

        if (!is_array($event)) {
            throw new InvalidArgumentException("Message name factory instance can handle array event only");
        }

        if (!isset($payload['message_name'])) {
            throw new RuntimeException("Missing message name key from array payload");
        }

        $headers = $payload['headers'] ?? [];

        $payload = [
            'headers' => $headers + [Header::EVENT_TYPE => $payload['message_name']],
            'content' => $payload['content'] ?? []
        ];

        $event = $this->serializer->unserializeContent($payload)->current();

        return $event instanceof Message ? $event : new Message($event);
    }
}
