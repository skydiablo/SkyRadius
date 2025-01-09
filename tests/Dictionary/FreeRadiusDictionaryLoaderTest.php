<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests\Dictionary;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\Dictionary\FreeRadiusDictionaryLoader;
use SkyDiablo\SkyRadius\SkyRadius;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IntegerAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;

class FreeRadiusDictionaryLoaderTest extends TestCase
{
    private FreeRadiusDictionaryLoader $loader;
    private SkyRadius $skyRadius;

    protected function setUp(): void
    {
        $this->skyRadius = $this->createMock(SkyRadius::class);
        $this->loader = new FreeRadiusDictionaryLoader($this->skyRadius);
    }

    public function testGetAttributeHandlerByType(): void
    {
        $this->assertInstanceOf(
            StringAttributeHandler::class,
            $this->loader->getAttributeHandlerByType('STRING')
        );
        
        $this->assertInstanceOf(
            IntegerAttributeHandler::class,
            $this->loader->getAttributeHandlerByType('INTEGER')
        );
        
        $this->assertInstanceOf(
            IPv4AttributeHandler::class,
            $this->loader->getAttributeHandlerByType('IPADDR')
        );
    }

    public function testInvalidAttributeType(): void
    {
        $this->expectException(\Exception::class);
        $this->loader->getAttributeHandlerByType('INVALID_TYPE');
    }
} 