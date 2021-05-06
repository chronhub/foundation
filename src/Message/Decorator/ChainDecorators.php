<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class ChainDecorators implements MessageDecorator
{
    private array $messageDecorators;

    public function __construct(MessageDecorator ...$messageDecorators)
    {
        $this->messageDecorators = $messageDecorators;
    }

    public function decorate(Message $message): Message
    {
        foreach ($this->messageDecorators as $messageDecorator) {
            $message = $messageDecorator->decorate($message);
        }

        return $message;
    }
}
