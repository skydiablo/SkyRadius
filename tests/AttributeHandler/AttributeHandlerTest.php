<?php

declare(strict_types=1);

namespace SkyRadius\Tests\AttributeHandler;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IntegerAttributeHandler;
use SkyDiablo\SkyRadius\Attribute\IPv4Attribute;
use SkyDiablo\SkyRadius\Attribute\IntegerAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\Exception\InvalidArgumentException;

class AttributeHandlerTest extends TestCase
{
    public function testIPv4AttributeHandlerValidation(): void
    {
        $handler = new IPv4AttributeHandler();
        /** @var PacketInterface $packet */
        $packet = $this->createMock(PacketInterface::class);
        
        // Test gültige IP
        $attr = new IPv4Attribute(4, "192.168.1.1");
        $serialized = $handler->serializeValue($attr, $packet);
        $this->assertNotNull($serialized);
        $this->assertEquals(4, strlen($serialized)); // IPv4 sollte 4 Bytes sein
        
        // Test ungültige IP
        $this->expectException(InvalidArgumentException::class);
        new IPv4Attribute(4, "256.256.256.256");
    }

    public function testIntegerAttributeHandlerBitSizes(): void 
    {
        $handler = new IntegerAttributeHandler();
        /** @var PacketInterface $packet */
        $packet = $this->createMock(PacketInterface::class);
        
        // 8-Bit Test
        $attr8bit = new IntegerAttribute(1, 255, IntegerAttribute::BIT_8);
        $serialized8 = $handler->serializeValue($attr8bit, $packet);
        $this->assertEquals(1, strlen($serialized8));
        
        // 16-Bit Test
        $attr16bit = new IntegerAttribute(1, 65535, IntegerAttribute::BIT_16);
        $serialized16 = $handler->serializeValue($attr16bit, $packet);
        $this->assertEquals(2, strlen($serialized16));
        
        // 32-Bit Test
        $attr32bit = new IntegerAttribute(1, 4294967295, IntegerAttribute::BIT_32);
        $serialized32 = $handler->serializeValue($attr32bit, $packet);
        $this->assertEquals(4, strlen($serialized32));
        
        // Test Überlauf
        $this->expectException(InvalidArgumentException::class);
        new IntegerAttribute(1, 256, IntegerAttribute::BIT_8);
    }
} 