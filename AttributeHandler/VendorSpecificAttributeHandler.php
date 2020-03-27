<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\VendorSpecificAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\AttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\AttributeHandlerInterface;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

class VendorSpecificAttributeHandler extends AbstractAttributeHandler
{

    /**
     * @var RawAttributeHandler
     */
    private $rawAttributeHandler;

    /**
     * @var AttributeHandler[]
     */
    private $attributeHandlerList = [];

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
    public function setHandler(int $vendorId, AttributeHandlerInterface $handler, int $type, string $alias = null, array $valueAlias = [])
    {
        $ah = $this->attributeHandlerList[$vendorId] ?? $this->attributeHandlerList[$vendorId] = new AttributeHandler();
        $ah->setHandler($handler, $type, $alias, $valueAlias);
        return $this;
    }


    /**
     * @inheritDoc
     * @see https://tools.ietf.org/html/rfc2865#section-5.26
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, RequestPacket $requestPacket)
    {
        $vendorId = $this->unpackInt32($rawAttribute->getValue()); //first 4 bytes are represent the vendor-id
        $vsaRawAttribute = $this->rawAttributeHandler->parseRawAttribute($rawAttribute->getValue(), 4); //skip first 4 bytes
        $ah = $this->attributeHandlerList[$vendorId] ?? null;
        if ($ah) {
            $attr = $ah->deserializeRawAttribute($vsaRawAttribute, $requestPacket);
            return new VendorSpecificAttribute($vendorId, $attr);
        }
    }

    /**
     * @return null|string
     * @var AttributeInterface|VendorSpecificAttribute $attribute
     */
    public function serializeValue(AttributeInterface $attribute)
    {
        $ah = $this->attributeHandlerList[$attribute->getVendorId()] ?? null;
        if ($ah) {
            /** @var VendorSpecificAttribute $attribute */
            $out = $this->packInt32($attribute->getVendorId());
            $out .= $ah->serializeAttribute($attribute->getInnerVSA());
            return $out;
        }
    }
}