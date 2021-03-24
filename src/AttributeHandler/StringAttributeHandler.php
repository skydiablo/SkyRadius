<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Packet\PacketInterface;

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
     * @param PacketInterface $requestPacket
     * @return StringAttribute
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        return new StringAttribute($rawAttribute->getType(), $rawAttribute->getValue());
    }

    /**
     * @param AttributeInterface $attribute
     * @param PacketInterface $requestPacket
     * @return string
     */
    public function serializeValue(AttributeInterface $attribute, PacketInterface $requestPacket): ?string
    {
        return $this->packValue($attribute->getValue());
    }

    /**
     * @param string $value
     * @return string
     */
    protected function packValue(string $value)
    {
        switch (true) {
            case (bool)preg_match(self::REGEX_HEX_VALUE, $value): // eg.: 0xFF00AACCBB55DD
                return hex2bin(substr($value, 2)); //@todo: maybe faster: return hex2bin(base_convert($value, 16, 16));
            // binary-string is just an feature, we can activate it maybe later...
//            case (bool)preg_match(self::REGEX_BIN_VALUE, $value): // eg.: 0b11010010000110101
//                return hex2bin(base_convert($value, 2, 16)); //@todo: is there any better option available?
            default:
                return $value;
        }
    }

}