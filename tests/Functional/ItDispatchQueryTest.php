<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional;

use Chronhub\Foundation\Reporter\ReportQuery;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Support\Traits\HandlePromise;
use Chronhub\Foundation\Tests\Double\SomeQuery;
use Chronhub\Foundation\Tests\Double\SomeQueryHandler;
use Chronhub\Foundation\Tests\OrchestraWithDefaultConfig;
use Ramsey\Uuid\UuidInterface;
use React\Promise\Deferred;

final class ItDispatchQueryTest extends OrchestraWithDefaultConfig
{
    use HandlePromise;

    /**
     * @test
     */
    public function it_dispatch_query(): void
    {
        $pastQuery = null;

        $this->app['config']->set('reporter.reporting.query.default.map', [
            'some-query' => function (SomeQuery $query, Deferred $promise) use (&$pastQuery): void {
                $pastQuery = $query;

                $promise->resolve($query->toContent()['name']);
            }
        ]);

        $query = SomeQuery::fromContent(['name' => 'steph']);

        $promise = Report::query()->publish($query);

        $this->assertInstanceOf(SomeQuery::class, $pastQuery);

        $headers = $pastQuery->headers();

        $this->assertEquals(ReportQuery::class, $headers[Header::REPORTER_NAME]);
        $this->assertInstanceOf(UuidInterface::class, $headers[Header::EVENT_ID]);
        $this->assertInstanceOf(PointInTime::class, $headers[Header::EVENT_TIME]);

        $result = $this->handlePromise($promise);

        $this->assertEquals('steph', $result);
    }

    /**
     * @test
     */
    public function it_dispatch_query_to_his_named_handler(): void
    {
        $handler = new SomeQueryHandler();

        $this->app['config']->set('reporter.reporting.query.default.map', [
            'some-query' => $handler
        ]);

        $query = SomeQuery::fromContent(['name' => 'steph']);

        $promise = Report::query()->publish($query);

        $result = $this->handlePromise($promise);

        $this->assertEquals(['name' => 'steph'], $result);
    }
}
