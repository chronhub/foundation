<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Factory;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageFactory;

final class MessageNameFactory implements MessageFactory
{
    public function __construct(private GenericMessageFactory $factory)
    {
        //
    }

    public function createFromMessage(object|array $payload): Message
    {
        // have to define a specific target for reporter
        // in reporter config bus name when dispatching async
        // as it would fail coming back here as default array if async

        if (!is_array($payload)) {
            throw new InvalidArgumentException("Message name factory instance can handle array event only");
        }

        $messageName = $payload['message_name'] ?? null;

        if (null === $messageName) {
            throw new InvalidArgumentException("Missing message name key from array payload");
        }

        if (!class_exists($messageName)) {
            throw new InvalidArgumentException("Message name must be a fqcn");
        }

        $headers = $payload['headers'] ?? [];

        $payload = [
            'headers' => $headers + [Header::EVENT_TYPE => $messageName],
            'content' => $payload['content'] ?? []
        ];

        return $this->factory->createFromMessage($payload);
    }
}
