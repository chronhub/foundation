<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Message\Decorator;

use Chronhub\Foundation\Message\Headers\StringableHeader;
use Chronhub\Foundation\Message\Message;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Support\Contracts\Message\MessageDecorator;

final class MarkReporterName implements MessageDecorator
{
    public function __construct(private string $reporterName)
    {
        //
    }

    public function decorate(Message $message): Message
    {
        if(!$message->has(Header::BUS_NAME)){
            $header = new StringableHeader(Header::BUS_NAME, $this->reporterName);

            $message = $message->withHeader($header);
        }

        return $message;
    }
}
