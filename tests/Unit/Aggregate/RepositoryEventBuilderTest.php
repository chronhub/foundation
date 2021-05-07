<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Aggregate;

use Chronhub\Foundation\Aggregate\GenericAggregateId;
use Chronhub\Foundation\Aggregate\RepositoryEventBuilder;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Aggregate\AggregateId;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;
use Chronhub\Foundation\Tests\Double\NoOpMessageDecorator;
use Chronhub\Foundation\Tests\Double\SomeAggregateChanged;
use Chronhub\Foundation\Tests\Double\SomeAggregateRoot;
use Chronhub\Foundation\Tests\TestCaseWithProphecy;
use Generator;
use Prophecy\Argument;
use function reset;

/** @coversDefaultClass \Chronhub\Foundation\Aggregate\RepositoryEventBuilder */
final class RepositoryEventBuilderTest extends TestCaseWithProphecy
{
    /**
     * @test
     */
    public function it_release_domain_events_from_aggregate_root(): void
    {
        $aggregateId = GenericAggregateId::create();

        $events = iterator_to_array($this->provideEvents($aggregateId, 2));

        $aggregateRoot = SomeAggregateRoot::create($aggregateId, $events);

        $this->assertEquals(2, $aggregateRoot->countEventsToRelease());

        $builder = new RepositoryEventBuilder(new NoOpMessageDecorator());

        $events = $builder->build($aggregateRoot);

        $this->assertCount(2, $events);
        $this->assertEquals(2, $aggregateRoot->version());
        $this->assertEquals(0, $aggregateRoot->countEventsToRelease());
    }

    /**
     * @test
     */
    public function it_update_domain_event_header(): void
    {
        $aggregateId = GenericAggregateId::create();

        $events = iterator_to_array($this->provideEvents($aggregateId, 5));

        $aggregateRoot = SomeAggregateRoot::create($aggregateId, $events);

        $builder = new RepositoryEventBuilder(new NoOpMessageDecorator());

        $events = $builder->build($aggregateRoot);

        $this->assertEquals(1, reset($events)->headers()[Header::AGGREGATE_VERSION]);

        foreach ($events as $event){
            $this->assertArrayHasKey(Header::AGGREGATE_ID, $event->headers());
            $this->assertArrayHasKey(Header::AGGREGATE_ID_TYPE, $event->headers());
            $this->assertArrayHasKey(Header::AGGREGATE_TYPE, $event->headers());

            $this->assertEquals($aggregateId->toString(), $event->headers()[Header::AGGREGATE_ID]);
            $this->assertEquals($aggregateId::class, $event->headers()[Header::AGGREGATE_ID_TYPE]);
            $this->assertEquals($aggregateRoot::class, $event->headers()[Header::AGGREGATE_TYPE]);
        }
    }

    /**
     * @test
     */
    public function it_decorate_domain_events(): void
    {
        $messageDecorator = $this->prophesize(MessageDecorator::class);

        $messageDecorator->decorate(Argument::type(Message::class))
            ->will(function(array $messages): Message{
                $message = array_shift($messages);

                return $message->withHeader('some_header', true);
            })->shouldBeCalledTimes(2);

        $aggregateId = GenericAggregateId::create();
        $events = iterator_to_array($this->provideEvents($aggregateId, 2));
        $aggregateRoot = SomeAggregateRoot::create($aggregateId, $events);

        $builder = new RepositoryEventBuilder($messageDecorator->reveal());

        $events = $builder->build($aggregateRoot);

        foreach($events as $event){
            $this->assertArrayHasKey('some_header', $event->headers());
            $this->assertTrue($event->headers()['some_header']);
        }
    }

    private function provideEvents(AggregateId $aggregateId, int $limit): Generator
    {
        $return = $limit;

        while ($limit !== 0) {
            yield SomeAggregateChanged::withData($aggregateId, []);

            $limit--;
        }

        return $return;
    }
}
