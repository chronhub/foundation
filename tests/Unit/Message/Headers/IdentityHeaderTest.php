<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Message\Headers\IdentityHeader;
use Chronhub\Foundation\Support\Contracts\Message\Header;
use Chronhub\Foundation\Tests\TestCase;
use Ramsey\Uuid\Uuid;

/** @coversDefaultClass \Chronhub\Foundation\Message\Headers\IdentityHeader */
final class IdentityHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_instantiated_from_string_id(): void
    {
        $uuid = Uuid::uuid4();

        $header = IdentityHeader::fromString($uuid->toString());

        $this->assertEquals($uuid, $header->toValue());
        $this->assertEquals(Header::EVENT_ID, $header->name());
        $this->assertEquals([Header::EVENT_ID => $uuid->toString()], $header->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $header = IdentityHeader::create();
        $uuid = $header->toValue();

        $this->assertEquals(Header::EVENT_ID, $header->name());
        $this->assertEquals([Header::EVENT_ID => $uuid->toString()], $header->jsonSerialize());
    }
}
