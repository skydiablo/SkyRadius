<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests\AttributeHandler;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

class StringAttributeHandlerTest extends TestCase
{
    private StringAttributeHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new StringAttributeHandler();
    }

    public function testDeserializeRawAttribute(): void
    {
        $type = 1;
        $value = "test-value";
        $rawAttr = new RawAttribute($type, strlen($value), $value, strlen($value) + 2);
        
        $packet = $this->createMock(RequestPacket::class);
        
        $result = $this->handler->deserializeRawAttribute($rawAttr, $packet);
        
        $this->assertInstanceOf(StringAttribute::class, $result);
        $this->assertEquals($type, $result->getType());
        $this->assertEquals($value, $result->getValue());
    }

    public function testSerializeValue(): void
    {
        $value = "test-value";
        $attr = new StringAttribute(1, $value);
        $packet = $this->createMock(RequestPacket::class);
        
        $result = $this->handler->serializeValue($attr, $packet);
        
        $this->assertEquals($value, $result);
    }

    public function testSerializeHexValue(): void
    {
        $hexValue = "0xFF00";
        $attr = new StringAttribute(1, $hexValue);
        $packet = $this->createMock(RequestPacket::class);
        
        $result = $this->handler->serializeValue($attr, $packet);
        
        $this->assertEquals(hex2bin(substr($hexValue, 2)), $result);
    }
} 