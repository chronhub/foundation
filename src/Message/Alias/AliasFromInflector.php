<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Alias;

use Chronhub\Foundation\Exception\InvalidArgumentException;
use Chronhub\Foundation\Support\Contracts\Message\MessageAlias;
use Illuminate\Support\Str;

final class AliasFromInflector implements MessageAlias
{
    public function classToAlias(string $eventClass): string
    {
        if (!class_exists($eventClass)) {
            throw new InvalidArgumentException("Event class $eventClass does not exists");
        }

        return $this->produceAlias($eventClass);
    }

    public function instanceToAlias(object $event): string
    {
        return $this->produceAlias($event::class);
    }

    private function produceAlias(string $eventClass): string
    {
        return Str::kebab(class_basename($eventClass));
    }
}
