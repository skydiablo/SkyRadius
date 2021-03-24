<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\VendorSpecificAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\AttributeManager;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

/**
 * Class VendorSpecificAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class VendorSpecificAttributeHandler extends AbstractAttributeHandler
{

    /**
     * @var RawAttributeHandler
     */
    private RawAttributeHandler $rawAttributeHandler;

    /**
     * @var AttributeManager[]
     */
    private array $attributeManagerList = [];

    /**
     * VendorSpecificAttributeHandler constructor.
     */
    public function __construct()
    {
        $this->rawAttributeHandler = new RawAttributeHandler();
    }

    /**
     * @param int $vendorId
     * @param AttributeHandlerInterface $handler
     * @param int $type
     * @param string|null $alias
     * @param array $valueAlias
     * @return VendorSpecificAttributeHandler
     */
    public function setHandler(int $vendorId, AttributeHandlerInterface $handler, int $type, string $alias = null, array $valueAlias = []): VendorSpecificAttributeHandler
    {
        $ah = $this->attributeManagerList[$vendorId] ?? $this->attributeManagerList[$vendorId] = new AttributeManager();
        $ah->setHandler($handler, $type, $alias, $valueAlias);
        return $this;
    }


    /**
     * @inheritDoc
     * @see https://tools.ietf.org/html/rfc2865#section-5.26
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        $vendorId = $this->unpackInt32($rawAttribute->getValue()); //first 4 bytes are represent the vendor-id
        $vsaRawAttribute = $this->rawAttributeHandler->parseRawAttribute($rawAttribute->getValue(), 4); //skip first 4 bytes
        $attributeHandler = $this->attributeManagerList[$vendorId] ?? null;
        if ($attributeHandler) {
            if ($attr = $attributeHandler->deserializeRawAttribute($vsaRawAttribute, $requestPacket)) {
                return new VendorSpecificAttribute($vendorId, $attr);
            }
        }
        return null;
    }

    /**
     * @param RequestPacket $requestPacket
     * @return null|string
     * @var AttributeInterface|VendorSpecificAttribute $attribute
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        $attributeHandler = $this->attributeManagerList[$attribute->getVendorId()] ?? null;
        if ($attributeHandler) {
            /** @var VendorSpecificAttribute $attribute */
            $out = $this->packInt32($attribute->getVendorId());
            $out .= $attributeHandler->serializeAttribute($attribute->getInnerVSA(), $requestPacket);
            return $out;
        }
        return null;
    }
}