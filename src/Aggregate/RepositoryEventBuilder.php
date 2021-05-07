<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Aggregate;

use Chronhub\Foundation\Message\DomainEvent;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateRoot;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class RepositoryEventBuilder
{
    public function __construct(private MessageDecorator $messageDecorator)
    {
        //
    }

    /**
     * @param AggregateRoot $aggregateRoot
     * @return array<DomainEvent>
     */
    public function build(AggregateRoot $aggregateRoot): array
    {
        $version = $aggregateRoot->version();
        $aggregateId = $aggregateRoot->aggregateId();
        $events = $aggregateRoot->releaseEvents();

        $version = $version - count($events);

        $headers = [
            Header::AGGREGATE_ID => $aggregateId->toString(),
            Header::AGGREGATE_TYPE => $aggregateRoot::class
        ];

        return tap(
            $events,
            fn(DomainEvent $event): DomainEvent => $this->map($event, $headers, $version)
        );
    }

    private function map(DomainEvent $event, array $headers, int &$version): DomainEvent
    {
        return tap(
            new Message($event, $headers + [Header::AGGREGATE_VERSION => ++$version]),
            fn(Message $message) => $this->messageDecorator->decorate($message)->event()
        );
    }
}
