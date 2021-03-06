<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius\AttributeHandler;


use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

class TunnelPasswordAttributeHandler extends AbstractAttributeHandler
{

    private $psk;

    /**
     * TunnelPasswordAttributeHandler constructor.
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
        return null; // TunnelPassword is only allowed on Accept-Response Packet, so deserialize is not permitted
    }

    /**
     * @param AttributeInterface $attribute
     * @param PacketInterface $requestPacket
     * @return string
     * @throws \Exception
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        $salt = \random_bytes(2);
        // The most significant bit (leftmost) of the Salt field MUST be set (1)
        $salt[0] = chr(ord($salt[0]) | (1 << 7)); //@todo: any ideas to simplification this?

        $out = $this->packInt8($attribute->getTag()) . $salt;

        $b = md5($this->psk . $requestPacket->getAuthenticator() . $salt, true);
        $p = str_split($attribute->getValue(), 16);
        // fill last element with 0x00 values to bring all chunks to 16 bytes
        $p[] = str_pad(array_pop($p), 16, chr(0x00), STR_PAD_RIGHT);
        foreach ($p as $subP) {
            $c = $subP ^ $b;
            $b = md5($this->psk . $c, true);
            $out .= $c;
        }
        return $out;
    }
}