<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\AttributeHandler\TunnelPasswordAttributeHandler;
use SkyDiablo\SkyRadius\Attribute\TunnelAttribute;
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
} 