<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests\AttributeHandler;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;
use SkyDiablo\SkyRadius\Attribute\IPv4Attribute;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

class IPv4AttributeHandlerTest extends TestCase
{
    private IPv4AttributeHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new IPv4AttributeHandler();
    }

    public function testDeserializeRawAttribute(): void
    {
        $type = 4;
        $ipLong = ip2long("192.168.1.1");
        $rawValue = pack("N", $ipLong);
        
        $rawAttr = new RawAttribute($type, strlen($rawValue), $rawValue, strlen($rawValue) + 2);
        $packet = $this->createMock(RequestPacket::class);
        
        $result = $this->handler->deserializeRawAttribute($rawAttr, $packet);
        
        $this->assertInstanceOf(IPv4Attribute::class, $result);
        $this->assertEquals($type, $result->getType());
        $this->assertEquals("192.168.1.1", $result->getValue());
    }

    public function testSerializeValue(): void
    {
        $ip = "192.168.1.1";
        $attr = new IPv4Attribute(4, $ip);
        $packet = $this->createMock(RequestPacket::class);
        
        $result = $this->handler->serializeValue($attr, $packet);
        
        $this->assertEquals(pack("N", ip2long($ip)), $result);
    }
} 