<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Reporter\Router;

use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Reporter\Router;

final class MultipleHandlerRouter implements Router
{
    public function __construct(private Router $router)
    {
    }

    public function route(Message $message): iterable
    {
        return $this->router->route($message);
    }
}
