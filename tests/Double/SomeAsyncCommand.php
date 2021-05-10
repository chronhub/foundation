<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Double;

use Chronhub\Foundation\Message\DomainCommand;
use Chronhub\Foundation\Support\Contracts\Message\AsyncMessage;

final class SomeAsyncCommand extends DomainCommand implements AsyncMessage
{
    //
}
