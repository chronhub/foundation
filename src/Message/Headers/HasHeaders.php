<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Headers;

use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\HeadingId;
use Chronhub\Foundation\Support\Contracts\Message\HeadingTime;
use Chronhub\Foundation\Support\Contracts\Message\HeadingType;

trait HasHeaders
{
    protected Headers $headers;

    public function eventId(): ?HeadingId
    {
        return $this->headers[Header::EVENT_ID] ?? null;
    }

    public function eventName(): ?HeadingType
    {
        return $this->headers[Header::EVENT_TYPE] ?? null;
    }

    public function eventTime(): ?HeadingTime
    {
        return $this->headers[Header::EVENT_TIME] ?? null;
    }

    public function has(string $header): bool
    {
        return isset($this->headers[$header]);
    }

    public function header(string $header): ?Header
    {
        return $this->headers[$header] ?? null;
    }

    public function headers(): Headers
    {
        return $this->headers;
    }
}
