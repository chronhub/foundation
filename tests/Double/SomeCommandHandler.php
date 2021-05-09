<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

final class SomeCommandHandler
{
    private bool $isHandled = false;

    public function command(SomeCommand $command): void
    {
        $this->isHandled = true;
    }

    public function isHandled(): bool
    {
        return $this->isHandled;
    }
}
