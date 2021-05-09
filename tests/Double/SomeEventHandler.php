<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

final class SomeEventHandler
{
    private bool $isHandled = false;

    public function onEvent(SomeEvent $event): void
    {
        $this->isHandled = true;
    }

    public function isHandled(): bool
    {
        return $this->isHandled;
    }
}
