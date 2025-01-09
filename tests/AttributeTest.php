<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Attribute\IntegerAttribute;
use SkyDiablo\SkyRadius\Attribute\IPv4Attribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;

class AttributeTest extends TestCase
{
    public function testStringAttribute(): void
    {
        $value = "test-value";
        $attr = new StringAttribute(AttributeInterface::ATTR_USER_NAME, $value);
        
        $this->assertEquals(AttributeInterface::ATTR_USER_NAME, $attr->getType());
        $this->assertEquals($value, $attr->getValue());
        
        // Test Alias
        $alias = "Test-Alias";
        $attr->setTypeAlias($alias);
        $this->assertEquals($alias, $attr->getTypeAlias());
    }

    public function testIntegerAttribute(): void
    {
        $value = 42;
        $attr = new IntegerAttribute(AttributeInterface::ATTR_NAS_PORT, $value, IntegerAttribute::BIT_32);
        
        $this->assertEquals(AttributeInterface::ATTR_NAS_PORT, $attr->getType());
        $this->assertEquals($value, $attr->getValue());
        $this->assertEquals(IntegerAttribute::BIT_32, $attr->getBit());
    }

    public function testIPv4Attribute(): void
    {
        $value = "192.168.1.1";
        $attr = new IPv4Attribute(AttributeInterface::ATTR_NAS_IP_ADDRESS, $value);
        
        $this->assertEquals(AttributeInterface::ATTR_NAS_IP_ADDRESS, $attr->getType());
        $this->assertEquals($value, $attr->getValue());
    }
} 