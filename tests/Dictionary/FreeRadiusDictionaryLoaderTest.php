<?php

declare(strict_types=1);

namespace SkyRadius\Tests\Dictionary;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\Dictionary\FreeRadiusDictionaryLoader;
use SkyDiablo\SkyRadius\SkyRadius;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;

class FreeRadiusDictionaryLoaderTest extends TestCase
{
    private $tempFile;
    
    protected function setUp(): void
    {
        $this->tempFile = tmpfile();
    }
    
    protected function tearDown(): void
    {
        if ($this->tempFile) {
            fclose($this->tempFile);
        }
    }

    public function testDictionaryParsing(): void
    {
        /** @var SkyRadius|MockObject $skyRadius */
        $skyRadius = $this->createMock(SkyRadius::class);
        $loader = new FreeRadiusDictionaryLoader($skyRadius);
        
        // Schreibe Test-Dictionary
        fwrite($this->tempFile, "VENDOR Cisco 9\n");
        fwrite($this->tempFile, "BEGIN-VENDOR Cisco\n");
        fwrite($this->tempFile, "ATTRIBUTE Cisco-AVPair 1 string\n");
        fwrite($this->tempFile, "END-VENDOR Cisco\n");
        
        // Prüfe Handler-Registrierung
        $skyRadius->expects($this->once())
                 ->method('setVsaHandler')
                 ->with(
                     $this->equalTo(9),
                     $this->isInstanceOf(StringAttributeHandler::class),
                     $this->equalTo(1),
                     $this->equalTo('Cisco-AVPair'),
                     $this->equalTo([])
                 );
        
        $loader->load(stream_get_meta_data($this->tempFile)['uri']);
    }

    public function testInvalidDictionaryFormat(): void
    {
        /** @var SkyRadius|MockObject $skyRadius */
        $skyRadius = $this->createMock(SkyRadius::class);
        $loader = new FreeRadiusDictionaryLoader($skyRadius);
        
        // Schreibe ungültiges Dictionary
        fwrite($this->tempFile, "INVALID LINE\n");
        
        $loader->load(stream_get_meta_data($this->tempFile)['uri']);
        $this->assertTrue(true); //if this is not thrown, the test is successful
    }
} 