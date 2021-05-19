<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Message\Alias;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;

final class AliasFromMap implements MessageAlias
{
    public function __construct(private array $map)
    {
    }

    public function classToAlias(string $eventClass): string
    {
        return $this->determineAlias($eventClass);
    }

    public function instanceToAlias(object $event): string
    {
        return $this->determineAlias($event::class);
    }

    private function determineAlias(string $eventClass): string
    {
        if ( ! class_exists($eventClass)) {
            throw new InvalidArgumentException("Event class $eventClass does not exists");
        }

        if ($alias = $this->map[$eventClass] ?? null) {
            return $alias;
        }

        throw new InvalidArgumentException("Event class $eventClass not found in alias map");
    }
}
