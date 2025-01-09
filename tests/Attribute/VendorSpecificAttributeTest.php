<?php

declare(strict_types=1);

namespace SkyRadius\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\AttributeHandler\VendorSpecificAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\Attribute\VendorSpecificAttribute;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\AttributeManager;

class VendorSpecificAttributeTest extends TestCase
{
    private AttributeManager $attributeManager;
    private VendorSpecificAttributeHandler $handler;

    protected function setUp(): void
    {
        $this->attributeManager = new AttributeManager();
        $this->handler = new VendorSpecificAttributeHandler();
        $this->attributeManager->setHandler(
            $this->handler,
            AttributeInterface::ATTR_VENDOR_SPECIFIC,
            'Vendor-Specific'
        );
        $this->handler->setHandler(
            9, // Cisco Vendor ID
            new StringAttributeHandler(),
            1,
            'Cisco-String-Attribute'
        );
    }

    public function testVendorAttributeHandling(): void
    {
        $packet = $this->createMock(PacketInterface::class);
        
        $innerAttr = new StringAttribute(1, 'test-value');
        $vsa = new VendorSpecificAttribute(9, $innerAttr); // Cisco = 9
        
        /** @var PacketInterface $packet */
        $serialized = $this->handler->serializeValue($vsa, $packet);
        
        // Prüfe Basis-Struktur
        $this->assertNotNull($serialized);
        $this->assertGreaterThan(4, strlen($serialized));
        
        // Prüfe Vendor-ID
        $vendorId = unpack('N', substr($serialized, 0, 4))[1];
        $this->assertEquals(9, $vendorId);
    }

    public function testMultipleVendorAttributes(): void
    {
        $packet = $this->createMock(PacketInterface::class);
        
        $innerAttr1 = new StringAttribute(1, 'value1');
        $innerAttr2 = new StringAttribute(1, 'value2'); // Beachte: gleicher Typ 1
        
        $vsa1 = new VendorSpecificAttribute(9, $innerAttr1);
        $vsa2 = new VendorSpecificAttribute(9, $innerAttr2);
        
        /** @var PacketInterface $packet */
        $serialized1 = $this->handler->serializeValue($vsa1, $packet);
        $serialized2 = $this->handler->serializeValue($vsa2, $packet);
        
        $this->assertNotNull($serialized1);
        $this->assertNotNull($serialized2);
        $this->assertNotEquals($serialized1, $serialized2);
    }
} 