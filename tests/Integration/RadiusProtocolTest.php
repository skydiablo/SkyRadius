<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Packet\RequestPacket;
use SkyDiablo\SkyRadius\Packet\ResponsePacket;
use SkyDiablo\SkyRadius\AttributeManager;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\UserPasswordAttributeHandler;

class RadiusProtocolTest extends TestCase
{
    private const TEST_SECRET = 'testing123';
    private AttributeManager $attributeManager;

    protected function setUp(): void
    {
        $this->attributeManager = new AttributeManager();
        
        // Setup basic handlers
        $this->attributeManager->setHandler(
            new StringAttributeHandler(),
            AttributeInterface::ATTR_USER_NAME,
            'User-Name'
        );
        
        $this->attributeManager->setHandler(
            new UserPasswordAttributeHandler(self::TEST_SECRET),
            AttributeInterface::ATTR_USER_PASSWORD,
            'User-Password'
        );
    }

    public function testAuthenticationRequest(): void
    {
        // Create a test request packet
        $requestPacket = new RequestPacket(
            RequestPacket::ACCESS_REQUEST,
            1, // identifier
            self::TEST_SECRET,
            random_bytes(16) // authenticator
        );

        // Add username attribute
        $username = new StringAttribute(
            AttributeInterface::ATTR_USER_NAME,
            'testuser'
        );
        $requestPacket->addAttribute($username);

        // Add password attribute
        $password = new StringAttribute(
            AttributeInterface::ATTR_USER_PASSWORD,
            'testpass'
        );
        $requestPacket->addAttribute($password);

        // Serialize the request
        $serializedRequest = '';
        foreach ($requestPacket->getAttributes() as $attribute) {
            $serializedValue = $this->attributeManager->serializeAttribute(
                $attribute,
                $requestPacket
            );
            $this->assertNotNull($serializedValue);
            $serializedRequest .= $serializedValue;
        }

        // Test packet contents
        $this->assertNotEmpty($serializedRequest);
        $this->assertEquals(RequestPacket::ACCESS_REQUEST, $requestPacket->getCode());
        $this->assertEquals(1, $requestPacket->getIdentifier());

        // Create and test response packet
        $responsePacket = new ResponsePacket(
            ResponsePacket::ACCESS_ACCEPT,
            $requestPacket->getIdentifier(),
            self::TEST_SECRET,
            $requestPacket->getAuthenticator()
        );

        // Add reply message
        $replyMessage = new StringAttribute(
            AttributeInterface::ATTR_REPLY_MESSAGE,
            'Access Granted'
        );
        $responsePacket->addAttribute($replyMessage);

        // Test response packet
        $this->assertEquals(ResponsePacket::ACCESS_ACCEPT, $responsePacket->getCode());
        $this->assertEquals($requestPacket->getIdentifier(), $responsePacket->getIdentifier());

        // Serialize response
        $serializedResponse = '';
        foreach ($responsePacket->getAttributes() as $attribute) {
            $serializedValue = $this->attributeManager->serializeAttribute(
                $attribute,
                $responsePacket
            );
            $this->assertNotNull($serializedValue);
            $serializedResponse .= $serializedValue;
        }

        $this->assertNotEmpty($serializedResponse);
    }

    public function testAccountingRequest(): void
    {
        // Create accounting request packet
        $requestPacket = new RequestPacket(
            RequestPacket::ACCOUNTING_REQUEST,
            2, // identifier
            self::TEST_SECRET,
            random_bytes(16) // authenticator
        );

        // Add accounting attributes
        $sessionId = new StringAttribute(
            AttributeInterface::ATTR_ACCT_SESSION_ID,
            'TEST-SESSION-01'
        );
        $requestPacket->addAttribute($sessionId);

        // Test packet
        $this->assertEquals(RequestPacket::ACCOUNTING_REQUEST, $requestPacket->getCode());
        $this->assertEquals(2, $requestPacket->getIdentifier());

        // Create accounting response
        $responsePacket = new ResponsePacket(
            ResponsePacket::ACCOUNTING_RESPONSE,
            $requestPacket->getIdentifier(),
            self::TEST_SECRET,
            $requestPacket->getAuthenticator()
        );

        $this->assertEquals(ResponsePacket::ACCOUNTING_RESPONSE, $responsePacket->getCode());
        $this->assertEquals($requestPacket->getIdentifier(), $responsePacket->getIdentifier());
    }

    public function testInvalidSecret(): void
    {
        $requestPacket = new RequestPacket(
            RequestPacket::ACCESS_REQUEST,
            3,
            'wrong_secret',
            random_bytes(16)
        );

        $this->assertNotEquals(self::TEST_SECRET, $requestPacket->getSecret());
    }

    public function testAttributeAliases(): void
    {
        $attr = new StringAttribute(AttributeInterface::ATTR_USER_NAME, 'testuser');
        $attr->setTypeAlias('User-Name');
        $attr->setValueAlias('TestUser');

        $this->assertEquals('User-Name', $attr->getTypeAlias());
        $this->assertEquals('TestUser', $attr->getValueAlias());
    }
} 