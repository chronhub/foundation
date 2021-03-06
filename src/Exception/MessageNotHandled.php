<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Exception;

final class MessageNotHandled extends ReportFailed
{
    public static function withMessageName(string $message): self
    {
        return new self("Message $message not handled");
    }
}
