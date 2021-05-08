<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Reporter\Subscribers;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Reporter\ReportCommand;
use Chronhub\Foundation\Reporter\ReportEvent;
use Chronhub\Foundation\Reporter\Subscribers\NameReporterService;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Reporter\Reporter;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tracker\TrackMessage;
use Generator;
use stdClass;

final class NameReporterServiceTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideReporterServiceId
     */
    public function it_mark_bus_name_header(string $serviceId): void
    {
        $tracker = new TrackMessage();

        $subscriber = new NameReporterService($serviceId);
        $subscriber->attachToTracker($tracker);

        $message = new Message(new stdClass());

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);

        $tracker->fire($context);

        $this->assertEquals($serviceId, $context->message()->header(Header::BUS_NAME));
    }

    /**
     * @test
     */
    public function it_does_not_mark_bus_name_header_if_already_exists(): void
    {
        $tracker = new TrackMessage();

        $subscriber = new NameReporterService('reporter.service_id');
        $subscriber->attachToTracker($tracker);

        $message = new Message(new stdClass(), [Header::BUS_NAME => 'my_service_id']);

        $context = $tracker->newContext(Reporter::DISPATCH_EVENT);
        $context->withMessage($message);

        $tracker->fire($context);

        $this->assertEquals('my_service_id', $context->message()->header(Header::BUS_NAME));
    }

    public function provideReporterServiceId(): Generator
    {
        yield ['reporter.service_id'];

        yield [ReportCommand::class,];

        yield [ReportEvent::class,];
    }
}
