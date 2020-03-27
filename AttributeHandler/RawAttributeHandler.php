<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;

/**
 * Class RawAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class RawAttributeHandler
{

    /**
     * @param string $data
     * @param int $pos start parsing at position in data
     * @return RawAttribute
     */
    public function parseRawAttribute(string $data, int $pos = 0)
    {
        $type = ord($data{$pos++});
        $attributeLength = ord($data{$pos++});
        $valueLength = $attributeLength - 2; // because "-2" => $type = 1byte + $attributeLength = 1byte
        $value = substr($data, $pos, $valueLength);
        return new RawAttribute($type, $valueLength, $value, $attributeLength);
    }


}