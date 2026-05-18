<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\AttributeManager;
use SkyDiablo\SkyRadius\AttributeHandler\AttributeHandlerInterface;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

class AttributeManagerTest extends TestCase
{
    public function testAttributeAliasing(): void
    {
        $manager = new AttributeManager();
        $packet = $this->createStub(PacketInterface::class);

        $handler = $this->createStub(AttributeHandlerInterface::class);
        $handler->method('serializeValue')
                ->willReturn('Administrator');
        
        // Handler mit Aliasing registrieren
        $manager->setHandler($handler, 1, 'User-Name', [
            'admin' => 'Administrator',
            'guest' => 'Guest User'
        ]);
        
        // Test mit Alias
        $attr = new StringAttribute(1, 'admin');
        /** @var PacketInterface $packet */
        $serialized = $manager->serializeAttribute($attr, $packet);
        
        $this->assertNotNull($serialized);
        $this->assertStringContainsString('Administrator', $serialized);
    }

    public function testUnknownAttributeType(): void
    {
        $manager = new AttributeManager();
        /** @var PacketInterface $packet */
        $packet = $this->createStub(PacketInterface::class);
        
        $serialized = $manager->serializeAttribute(
            new StringAttribute(999, 'test'),
            $packet
        );
        $this->assertNull($serialized); //if this is not thrown, the test is successful
    }

    public function testAttributeTypeRegistration(): void
    {
        $manager = new AttributeManager();
        $handler = $this->createStub(AttributeHandlerInterface::class);
        
        $manager->setHandler($handler, 1, 'Test-Attribute');
        
        // Prüfe ob Handler registriert wurde
        $this->assertNotNull($manager->getHandler(1));
        $this->assertNull($manager->getHandler(999));
    }
} 