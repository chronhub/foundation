<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Domain;

interface Messaging extends Content
{
    public const COMMAND = 'command';
    public const QUERY = 'query';
    public const EVENT = 'event';
    public const TYPES = [self::COMMAND, self::QUERY, self::EVENT];

    public function withHeaders(array $headers): Domain;

    public function withHeader(string $header, mixed $value): Domain;

    public function has(string $header): bool;

    public function header(string $header): mixed;

    public function headers(): array;

    public function type(): string;
}
