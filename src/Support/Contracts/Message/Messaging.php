<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use Chronhub\Foundation\Message\Domain;
use Chronhub\Foundation\Message\Headers\Headers;

interface Messaging extends Content
{
    public const COMMAND = 'command';
    public const QUERY = 'query';
    public const EVENT = 'event';
    public const TYPES = [self::COMMAND, self::QUERY, self::EVENT];

    public function withHeaders(Header ...$headers): Domain;

    public function withHeader(Header $header): Domain;

    public function has(string $header): bool;

    public function header(string $header): ?Header;

    public function headers(): Headers;

    public function type(): string;

    public function eventId(): ?HeadingId;

    public function eventTime(): ?HeadingTime;
}
