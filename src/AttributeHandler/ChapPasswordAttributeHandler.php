<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\ChapPasswordAttribute;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

/**
 * Class ChapPasswordAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class ChapPasswordAttributeHandler implements AttributeHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, RequestPacket $requestPacket)
    {
        $rawValue = $rawAttribute->getValue();
        return new ChapPasswordAttribute($rawAttribute->getType(), $rawValue{0}, substr($rawValue, 1, 16));
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute)
    {
        /** @var ChapPasswordAttribute $attribute */
        return $attribute->getChapResponse() . $attribute->getChapResponse();
    }

}