<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\AttributeHandler\TunnelPasswordAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\TunnelAttributeHandler;
use SkyDiablo\SkyRadius\Attribute\TunnelAttribute;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

class TunnelAttributeTest extends TestCase
{
    public function testTunnelPasswordEncryption(): void
    {
        $secret = 'testing123';
        $handler = new TunnelPasswordAttributeHandler($secret);
        
        $packet = $this->createMock(PacketInterface::class);
        $packet->method('getAuthenticator')
               ->willReturn(str_repeat('A', 16));
        
        $password = 'test-password';
        $attr = new TunnelAttribute(69, 1, $password);
        
        $encrypted = $handler->serializeValue($attr, $packet);
        
        $this->assertNotNull($encrypted);
        $this->assertNotEquals($password, $encrypted);
        $this->assertGreaterThan(strlen($password), strlen($encrypted));
        
        // Salt sollte die ersten 2 Bytes sein
        $this->assertEquals(2, strlen(substr($encrypted, 0, 2)));
    }

    public function testTunnelPasswordMinimumLength(): void
    {
        $handler = new TunnelPasswordAttributeHandler('secret');
        $packet = $this->createMock(PacketInterface::class);
        $packet->method('getAuthenticator')
               ->willReturn(str_repeat('A', 16));
        
        $attr = new TunnelAttribute(69, 1, 'x');  // Sehr kurzes Passwort
        
        $encrypted = $handler->serializeValue($attr, $packet);
        // Ergebnis sollte mindestens 16 Bytes sein (MD5 Block-Größe)
        $this->assertGreaterThanOrEqual(16, strlen($encrypted));
    }

    public function testTunnelPrivateGroupIdSerialization(): void
    {
        $handler = new TunnelAttributeHandler();
        $packet = $this->createMock(PacketInterface::class);
        
        $groupId = 'VLAN-100';
        $tag = 1;
        $attr = new TunnelAttribute(AttributeInterface::ATTR_TUNNEL_PRIVATE_GROUP_ID, $tag, $groupId);
        
        $serialized = $handler->serializeValue($attr, $packet);
        
        $this->assertNotNull($serialized);
        $this->assertGreaterThanOrEqual(strlen($groupId) + 1, strlen($serialized));
        
        // First byte should be the tag
        $this->assertEquals($tag, ord($serialized[0]));
        
        // Rest should be the group ID
        $this->assertEquals($groupId, substr($serialized, 1));
    }

    public function testTunnelPrivateGroupIdDeserialization(): void
    {
        $handler = new TunnelAttributeHandler();
        $packet = $this->createMock(PacketInterface::class);
        
        $groupId = 'VLAN-100';
        $tag = 1;
        $value = chr($tag) . $groupId;
        
        $rawAttr = new RawAttribute(
            AttributeInterface::ATTR_TUNNEL_PRIVATE_GROUP_ID,
            strlen($value),
            $value,
            strlen($value) + 2 // type (1 byte) + length (1 byte) + value
        );
        
        $deserialized = $handler->deserializeRawAttribute($rawAttr, $packet);
        
        $this->assertNotNull($deserialized);
        $this->assertInstanceOf(TunnelAttribute::class, $deserialized);
        $this->assertEquals(AttributeInterface::ATTR_TUNNEL_PRIVATE_GROUP_ID, $deserialized->getType());
        $this->assertEquals($groupId, $deserialized->getValue());
        $this->assertEquals($tag, $deserialized->getTag());
    }

    public function testTunnelPrivateGroupIdWithDifferentTags(): void
    {
        $handler = new TunnelAttributeHandler();
        $packet = $this->createMock(PacketInterface::class);
        
        $groupId = 'VLAN-200';
        
        // Test with tag 0
        $attr0 = new TunnelAttribute(AttributeInterface::ATTR_TUNNEL_PRIVATE_GROUP_ID, 0, $groupId);
        $serialized0 = $handler->serializeValue($attr0, $packet);
        $this->assertEquals(0, ord($serialized0[0]));
        
        // Test with tag 31 (maximum valid tag)
        $attr31 = new TunnelAttribute(AttributeInterface::ATTR_TUNNEL_PRIVATE_GROUP_ID, 31, $groupId);
        $serialized31 = $handler->serializeValue($attr31, $packet);
        $this->assertEquals(31, ord($serialized31[0]));
        
        // Test round-trip
        $rawAttr = new RawAttribute(
            AttributeInterface::ATTR_TUNNEL_PRIVATE_GROUP_ID,
            strlen($serialized31),
            $serialized31,
            strlen($serialized31) + 2
        );
        $deserialized = $handler->deserializeRawAttribute($rawAttr, $packet);
        $this->assertEquals(31, $deserialized->getTag());
        $this->assertEquals($groupId, $deserialized->getValue());
    }
} 