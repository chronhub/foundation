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
                                public string $busType,
                                public ?string $connection,
                                public ?string $queue)
    {
    }

    public function handle(Container $container): void
    {
        $serviceBus = $container->make($this->busType);

        $serviceBus->publish($this->payload);
    }

    /**
     * @param Queue      $queue
     * @param MessageJob $messageJob
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
