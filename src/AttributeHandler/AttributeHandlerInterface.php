<?php

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

/**
 * Interface AttributeHandlerInterface
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
interface AttributeHandlerInterface
{

    /**
     * @param RawAttribute $rawAttribute
     * @param PacketInterface $requestPacket
     * @return AttributeInterface|null
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface;

    /**
     * @param AttributeInterface $attribute
     * @param PacketInterface $requestPacket
     * @return string|null
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string;

}