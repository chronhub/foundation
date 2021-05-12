<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

final class SomeMessage
{
    public function __construct(private string $text)
    {
        //
    }

    public function getText(): string
    {
        return $this->text;
    }

}
