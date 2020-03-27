<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius\AttributeHandler;


use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Attribute\UserPasswordAttribute;
use SkyDiablo\SkyRadius\Packet\Packet;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

/**
 * Class UserPasswordAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class UserPasswordAttributeHandler implements AttributeHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, RequestPacket $requestPacket)
    {
        return new StringAttribute($rawAttribute->getType(), $this->decryptUserPasswordPAP($rawAttribute->getValue(), $requestPacket));
    }

    /**
     * @param string $encryptedPassword
     * @param RequestPacket $packet
     * @return string
     * @see https://tools.ietf.org/html/rfc2865#section-5.2
     */
    protected function decryptUserPasswordPAP(string $encryptedPassword, RequestPacket $packet)
    {
        $result = '';
        $salt = $packet->getAuthenticator();
        $psk = $packet->getPsk();
        foreach (str_split($encryptedPassword, 16) as $chunk) {
            $v = md5($psk . $salt, true);
            $result .= $chunk ^ $v; // XOR
            $salt = $chunk;
        }

        return rtrim($result, "\0");
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute)
    {
//        throw new \RuntimeException('Not implemented, yet!');
        return 'Server-Side "User-Password" encoding not supported!';
    }
}