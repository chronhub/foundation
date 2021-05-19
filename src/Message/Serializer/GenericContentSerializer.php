<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Serializer;

use Chronhub\Foundation\Aggregate\AggregateChanged;
use Chronhub\Foundation\Exception\RuntimeException;
use Chronhub\Foundation\Support\Contracts\Message\Content;
use Chronhub\Foundation\Support\Contracts\Message\Header;

final class GenericContentSerializer
{
    public function serialize(Content $event): array
    {
        return $event->toContent();
    }

    public function unserialize(string $source, array $payload): Content
    {
        if (is_subclass_of($source, AggregateChanged::class)) {
            $aggregateId = $payload['headers'][Header::AGGREGATE_ID];

            return $source::occur($aggregateId, $payload['content']);
        }

        if (is_subclass_of($source, Content::class)) {
            return $source::fromContent($payload['content']);
        }

        throw new RuntimeException('Invalid source');
    }
}
