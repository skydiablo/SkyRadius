<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\IntegerAttribute;
use SkyDiablo\SkyRadius\Attribute\IPv4Attribute;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Attribute\VendorSpecificAttribute;
use SkyDiablo\SkyRadius\AttributeHandler\IntegerAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\VendorSpecificAttributeHandler;
use SkyDiablo\SkyRadius\AttributeManager;
use SkyDiablo\SkyRadius\Exception\RangeException;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

class RadiusProtocolExtendedTest extends TestCase
{
    private const TEST_SECRET = 'testing123';
    private AttributeManager $attributeManager;
    private VendorSpecificAttributeHandler $vsaHandler;

    protected function setUp(): void
    {
        $this->attributeManager = new AttributeManager();
        $this->vsaHandler = new VendorSpecificAttributeHandler();
        
        // Setup VSA handler
        $this->attributeManager->setHandler(
            $this->vsaHandler,
            AttributeInterface::ATTR_VENDOR_SPECIFIC,
            'Vendor-Specific'
        );
    }

    /**
     * Test für Vendor Specific Attributes (VSAs)
     */
    public function testVendorSpecificAttributes(): void
    {
        // Cisco VSA (Vendor ID: 9)
        $this->vsaHandler->setHandler(
            9, // Cisco Vendor ID
            new StringAttributeHandler(),
            1, // AVPair
            'Cisco-AVPair'
        );

        // Create VSA
        $ciscoAvPair = new StringAttribute(1, 'shell:priv-lvl=15');
        $vsaAttribute = new VendorSpecificAttribute(9, $ciscoAvPair);

        // Create packet and add VSA
        $packet = new RequestPacket(
            RequestPacket::ACCESS_REQUEST,
            1,
            self::TEST_SECRET,
            random_bytes(16)
        );
        $packet->addAttribute($vsaAttribute);

        // Serialize and test
        $serializedVsa = $this->attributeManager->serializeAttribute($vsaAttribute, $packet);
        $this->assertNotNull($serializedVsa);
        $this->assertNotEmpty($serializedVsa);
    }

    /**
     * Test für verschiedene Attribut-Typen
     */
    public function testDifferentAttributeTypes(): void
    {
        // Setup handlers for different types
        $this->attributeManager->setHandler(
            new IPv4AttributeHandler(),
            AttributeInterface::ATTR_NAS_IP_ADDRESS,
            'NAS-IP-Address'
        );
        
        $this->attributeManager->setHandler(
            new IntegerAttributeHandler(),
            AttributeInterface::ATTR_NAS_PORT,
            'NAS-Port'
        );

        // Test IPv4 Attribute
        $ipAttr = new IPv4Attribute(
            AttributeInterface::ATTR_NAS_IP_ADDRESS,
            '192.168.1.1'
        );
        
        // Test Integer Attribute
        $portAttr = new IntegerAttribute(
            AttributeInterface::ATTR_NAS_PORT,
            1812,
            IntegerAttribute::BIT_32
        );

        $packet = new RequestPacket(
            RequestPacket::ACCESS_REQUEST,
            1,
            self::TEST_SECRET,
            random_bytes(16)
        );

        $packet->addAttribute($ipAttr);
        $packet->addAttribute($portAttr);

        // Test serialization of both types
        $serializedIp = $this->attributeManager->serializeAttribute($ipAttr, $packet);
        $serializedPort = $this->attributeManager->serializeAttribute($portAttr, $packet);

        $this->assertNotNull($serializedIp);
        $this->assertNotNull($serializedPort);
    }

    /**
     * Test für Error-Handling
     */
    public function testErrorHandling(): void
    {
        // Test invalid attribute length
        $this->expectException(\AssertionError::class);
        new StringAttribute(
            AttributeInterface::ATTR_USER_NAME,
            str_repeat('a', 254) // Too long (max 253 bytes)
        );

        // Test invalid IP format
        $this->expectException(\AssertionError::class);
        new IPv4Attribute(
            AttributeInterface::ATTR_NAS_IP_ADDRESS,
            'invalid.ip.address'
        );

        // Test invalid integer bit size
        $this->expectException(\InvalidArgumentException::class);
        new IntegerAttribute(
            AttributeInterface::ATTR_NAS_PORT,
            1812,
            128 // Invalid bit size
        );
    }

    /**
     * Test für Attribute Value Validation
     */
    public function testAttributeValueValidation(): void
    {
        // Test integer range validation
        $this->expectException(RangeException::class);
        new IntegerAttribute(
            AttributeInterface::ATTR_NAS_PORT,
            -1, // Negative values not allowed
            IntegerAttribute::BIT_32
        );

        // Test maximum integer value
        $maxValue = (2 ** 32) - 1;
        $attr = new IntegerAttribute(
            AttributeInterface::ATTR_NAS_PORT,
            $maxValue,
            IntegerAttribute::BIT_32
        );
        $this->assertEquals($maxValue, $attr->getValue());

        // Test overflow
        $this->expectException(RangeException::class);
        new IntegerAttribute(
            AttributeInterface::ATTR_NAS_PORT,
            2 ** 32, // Too large for 32 bits
            IntegerAttribute::BIT_32
        );
    }
} 