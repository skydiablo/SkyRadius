<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

/**
 * Class UserPasswordAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class UserPasswordAttributeHandler implements AttributeHandlerInterface
{

    private $psk;

    /**
     * UserPasswordAttributeHandler constructor.
     * @param $psk
     */
    public function __construct($psk)
    {
        $this->psk = $psk;
    }

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        return new StringAttribute($rawAttribute->getType(), $this->encodeUserPasswordPAP($requestPacket, $rawAttribute->getValue()));
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        return $this->encodeUserPasswordPAP($requestPacket, $attribute->getValue());
    }

    /**
     * @param PacketInterface $requestPacket
     * @param string $value
     * @return string
     * @see https://tools.ietf.org/html/rfc2865#section-5.2
     */
    protected function encodeUserPasswordPAP(PacketInterface $requestPacket, string $value): string
    {
        $result = '';
        $salt = $requestPacket->getAuthenticator();
        foreach (str_split($value, 16) as $chunk) {
            $v = md5($this->psk . $salt, true);
            $result .= $chunk ^ $v; // XOR
            $salt = $chunk;
        }
        return rtrim($result, "\0");
    }


}