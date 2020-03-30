<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

/**
 * Class StringAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class StringAttributeHandler implements AttributeHandlerInterface
{

    /**
     * @param RawAttribute $rawAttribute
     * @param RequestPacket $requestPacket
     * @return StringAttribute
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, RequestPacket $requestPacket)
    {
        return new StringAttribute($rawAttribute->getType(), $rawAttribute->getValue());
    }

    /**
     * @param AttributeInterface $attribute
     * @return string
     */
    public function serializeValue(AttributeInterface $attribute)
    {
        return $attribute->getValue();
    }

}