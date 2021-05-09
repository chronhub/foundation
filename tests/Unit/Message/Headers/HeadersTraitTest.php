<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Tests\TestCase;

final class HeadersTraitTest extends TestCase
{
    public function it_test(): void
    {
        $message = new SomeMessage();

        $message->event_type ='foo';
        $message->event_id ='145';



        dump($message->toArray());
    }
}
