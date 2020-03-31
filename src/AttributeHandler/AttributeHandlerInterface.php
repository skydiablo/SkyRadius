<?php

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

/**
 * Interface AttributeHandlerInterface
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
interface AttributeHandlerInterface
{

    /**
     * @param RawAttribute $rawAttribute
     * @param RequestPacket $requestPacket
     * @return AttributeInterface|null
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, RequestPacket $requestPacket);

    /**
     * @param AttributeInterface $attribute
     * @param RequestPacket $requestPacket
     * @return string|null
     */
    public function serializeValue(AttributeInterface $attribute, RequestPacket $requestPacket);

}