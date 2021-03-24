<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\IPv4Attribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

/**
 * Class IPv4AttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class IPv4AttributeHandler extends AbstractAttributeHandler
{

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        return new IPv4Attribute($rawAttribute->getType(), $ip = long2ip($this->unpackInt32($rawAttribute->getValue())));
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        return $this->packInt32(ip2long($attribute->getValue()));
    }
}