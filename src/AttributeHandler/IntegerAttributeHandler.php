<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\IntegerAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\Helper\UnPackInteger;

/**
 * Class Integer32AttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class IntegerAttributeHandler implements AttributeHandlerInterface
{

    use UnPackInteger;

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        // because "*8" => byte -> bit
        $bit = ($rawAttribute->getValueLength()) * 8;
        return new IntegerAttribute($rawAttribute->getType(), $this->unpackInt($bit, $rawAttribute->getValue()), $bit);
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        /** @var IntegerAttribute $attribute */
        return $this->packInt($attribute->getBit(), $attribute->getValue());
    }
}