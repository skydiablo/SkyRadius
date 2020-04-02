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

    const REGEX_HEX_VALUE = '!^0x[[:xdigit:]]+$!i';
    const REGEX_BIN_VALUE = '!^0b[01]+$!i';

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
     * @param RequestPacket $requestPacket
     * @return string
     */
    public function serializeValue(AttributeInterface $attribute, RequestPacket $requestPacket)
    {
        $value = $attribute->getValue();
        switch (true) {
            case (bool)preg_match(self::REGEX_HEX_VALUE, $value): // eg.: 0xFF00AACCBB55DD
                return hex2bin(substr($value, 2)); //@todo: maybe faster: return hex2bin(base_convert($value, 16, 16));
            case (bool)preg_match(self::REGEX_BIN_VALUE, $value): // eg.: 0b11010010000110101
                return hex2bin(base_convert($value, 2, 16)); //@todo: is there any better option available?
            default:
                return $value;
        }
    }

}