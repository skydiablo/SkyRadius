<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\TunnelAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

class Tunnel3ByteValueAttributeHandler extends AbstractAttributeHandler
{

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        return new TunnelAttribute($rawAttribute->getType(), $this->unpackInt8($rawAttribute->getValue()), substr($rawAttribute->getValue(), 1, 3));
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        /** @var TunnelAttribute $attribute */
        return $this->packInt8($attribute->getTag()) . $this->packIntByFormat('ccc',[$attribute->getValue() >> 16, $attribute->getValue() >> 8, $attribute->getValue()]);
    }
}