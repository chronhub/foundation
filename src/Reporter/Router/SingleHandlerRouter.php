<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Router;

use Chronhub\Foundation\Exception\ReportFailed;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Reporter\Router;

final class SingleHandlerRouter implements Router
{
    public function __construct(private Router $router)
    {
    }

    public function route(Message $message): iterable
    {
        $messageHandlers = $this->router->route($message);

        if (1 !== count($messageHandlers)) {
            throw ReportFailed::oneMessageHandlerOnly();
        }

        return $messageHandlers;
    }
}
