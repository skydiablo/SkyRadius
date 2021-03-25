<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Helper\UnPackInteger;

/**
 * Class RawAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class RawAttributeHandler
{

    use UnPackInteger;

    /**
     * @param string $data
     * @param int $pos start parsing at position in data
     * @return RawAttribute
     */
    public function parseRawAttribute(string $data, int $pos = 0): RawAttribute
    {
        $type = $this->unpackInt8($data, $pos++);
        $attributeLength = $this->unpackInt8($data, $pos++);
        $valueLength = $attributeLength - 2; // because "-2" => $type = 1byte + $attributeLength = 1byte
        $value = substr($data, $pos, $valueLength);
        return new RawAttribute($type, $valueLength, $value, $attributeLength);
    }

    /**
     * @param RawAttribute $rawAttribute
     * @return string
     */
    public function serializeRawAttribute(RawAttribute $rawAttribute): string
    {
        $out = $this->packInt8($rawAttribute->getType());
        $out .= $this->packInt8($rawAttribute->getAttributeLength());
        $out .= $rawAttribute->getValue();
        return $out;
    }

}