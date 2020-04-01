<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius\AttributeHandler;


use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\TunnelAttribute;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

class TunnelPasswordAttributeHandler extends AbstractAttributeHandler
{

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, RequestPacket $requestPacket)
    {
        return null; // TunnelPassword is only allowed on Accept-Response Packet, so deserialize is not permitted
    }

    /**
     * @param AttributeInterface|TunnelAttribute $attribute
     * @param RequestPacket $requestPacket
     * @return string
     * @throws \Exception
     */
    public function serializeValue(AttributeInterface $attribute, RequestPacket $requestPacket)
    {
        $psk = $requestPacket->getPsk();
        $salt = \random_bytes(2);
        // The most significant bit (leftmost) of the Salt field MUST be set (1)
        $salt[0] = chr(ord($salt[0]) | (1 << 7)); //@todo: any ideas to simplification this?

        $out = $this->packInt8($attribute->getTag()) . $salt;

        $b = md5($psk . $requestPacket->getAuthenticator() . $salt, true);
        $p = str_split($attribute->getValue(), 16);
        // fill last element with 0x00 values to bring all chunks to 16 bytes
        $p[] = str_pad(array_pop($p), 16, 0x00, STR_PAD_RIGHT);
        foreach ($p as $subP) {
            $c = $subP ^ $b;
            $b = md5($psk . $c);
            $out .= $c;
        }
        return $out;
    }
}