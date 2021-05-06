<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message;

use Chronhub\Foundation\Message\Headers\HasHeaders;
use Chronhub\Foundation\Message\Headers\Headers;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\Messaging;

abstract class Domain implements Messaging
{
    use HasHeaders;

    final protected function __construct(protected array $content)
    {
    }

    public function toContent(): array
    {
        return $this->content;
    }

    public static function fromContent(array $content): static
    {
        return new static($content);
    }

    // do we keep this ?
    public function jsonSerialize(): array
    {
        return [
            'headers' => $this->headers->jsonSerialize(),
            'content' => $this->toContent(),
        ];
    }

    public function withHeader(Header $header): Domain
    {
        $domain = clone $this;

        $domain->headers[$header->name()] = $header;

        return $domain;
    }

    public function withHeaders(Header ...$headers): Domain
    {
        $domain = clone $this;

        $domain->headers = new Headers(...$headers);

        return $domain;
    }
}
