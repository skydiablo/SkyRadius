<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\ChapPasswordAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

/**
 * Class ChapPasswordAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class ChapPasswordAttributeHandler implements AttributeHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        $rawValue = $rawAttribute->getValue();
        return new ChapPasswordAttribute($rawAttribute->getType(), $rawValue[0], substr($rawValue, 1, 16));
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        /** @var ChapPasswordAttribute $attribute */
        return $attribute->getChapIdent() . $attribute->getChapResponse();
    }

}