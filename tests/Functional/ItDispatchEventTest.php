<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Functional;

use Chronhub\Foundation\Reporter\ReportEvent;
use Chronhub\Foundation\Support\Contracts\Clock\PointInTime;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Facade\Report;
use Chronhub\Foundation\Tests\Double\SomeEvent;
use Chronhub\Foundation\Tests\Double\SomeEventHandler;
use Chronhub\Foundation\Tests\TestCaseWithOrchestra;
use Ramsey\Uuid\UuidInterface;

final class ItDispatchEventTest extends TestCaseWithOrchestra
{
    /**
     * @test
     */
    public function it_dispatch_event(): void
    {
        $pastEvent = null;

        $this->app['config']->set('reporter.reporting.event.default.map', [
            'some-event' => function (SomeEvent $event) use (&$pastEvent): void {
                $pastEvent = $event;
            }
        ]);

        $event = SomeEvent::fromContent(['name' => 'steph']);

        Report::event()->publish($event);

        $this->assertInstanceOf(SomeEvent::class, $pastEvent);

        $headers = $pastEvent->headers();

        $this->assertEquals(ReportEvent::class, $headers[Header::BUS_NAME]);
        $this->assertInstanceOf(UuidInterface::class, $headers[Header::EVENT_ID]);
        $this->assertInstanceOf(PointInTime::class, $headers[Header::EVENT_TIME]);
    }

    /**
     * @test
     */
    public function it_dispatch_event_to_his_named_handler(): void
    {
        $eventHandler = new SomeEventHandler();

        $this->app['config']->set('reporter.reporting.event.default.map', [
            'some-event' => $eventHandler
        ]);

        $event = SomeEvent::fromContent(['name' => 'steph']);

        $reporter = Report::event();
        $reporter->publish($event);

        $this->assertTrue($eventHandler->isHandled());
    }
}
