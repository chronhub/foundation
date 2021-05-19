<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Producer;

use Chronhub\Foundation\Support\Contracts\Message\Header;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\InteractsWithQueue;

final class MessageJob
{
    use InteractsWithQueue;

    public function __construct(public array $payload,
                                public string $reporterName,
                                public ?string $connection,
                                public ?string $queue)
    {
    }

    public function handle(Container $container): void
    {
        $container->make($this->reporterName)->publish($this->payload);
    }

    /**
     * @internal
     */
    public function queue(Queue $queue, MessageJob $messageJob): void
    {
        $queue->pushOn($this->queue, $messageJob);
    }

    public function displayName(): string
    {
        return $this->payload['headers'][Header::EVENT_TYPE];
    }
}
