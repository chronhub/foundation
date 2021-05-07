<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Tracker;

use Chronhub\Foundation\Support\Contracts\Tracker\TrackerContext;
use Chronhub\Foundation\Tests\TestCase;
use Chronhub\Foundation\Tracker\HasTrackerContext;
use RuntimeException;

/** @coversDefaultClass \Chronhub\Foundation\Tracker\HasTrackerContext */
final class TrackerContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $context = $this->newTrackerContext('dispatch');

        $this->assertEquals('dispatch', $context->currentEvent());
        $this->assertFalse($context->isPropagationStopped());
        $this->assertFalse($context->hasException());
        $this->assertNull($context->exception());
    }

    /**
     * @test
     */
    public function it_set_new_event(): void
    {
        $context = $this->newTrackerContext('dispatch');

        $this->assertEquals('dispatch', $context->currentEvent());

        $context->withEvent('finalize');

        $this->assertEquals('finalize', $context->currentEvent());
    }

    /**
     * @test
     */
    public function it_stop_propagation_of_event(): void
    {
        $context = $this->newTrackerContext('dispatch');

        $this->assertFalse($context->isPropagationStopped());

        $context->stopPropagation(true);

        $this->assertTrue($context->isPropagationStopped());
    }

    /**
     * @test
     */
    public function it_set_exception(): void
    {
        $context = $this->newTrackerContext('dispatch');

        $this->assertFalse($context->hasException());
        $this->assertNull($context->exception());

        $exception = new RuntimeException('failed');

        $context->withRaisedException($exception);

        $this->assertTrue($context->hasException());
        $this->assertEquals($exception, $context->exception());
    }

    /**
     * @test
     */
    public function it_can_reset_exception(): void
    {
        $context = $this->newTrackerContext('dispatch');

        $exception = new RuntimeException('failed');

        $context->withRaisedException($exception);

        $this->assertEquals($exception, $context->exception());

        $this->assertTrue($context->resetException());

        $this->assertNull($context->exception());
    }

    public function newTrackerContext(?string $event): TrackerContext
    {
        return new class($event) implements TrackerContext {
            use HasTrackerContext;
        };
    }
}
