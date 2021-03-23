<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

/**
 * Class UserPasswordAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
class UserPasswordAttributeHandler implements AttributeHandlerInterface
{

    private $psk;

    /**
     * UserPasswordAttributeHandler constructor.
     * @param $psk
     */
    public function __construct($psk)
    {
        $this->psk = $psk;
    }

    /**
     * @inheritDoc
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, RequestPacket $requestPacket)
    {
        return new StringAttribute($rawAttribute->getType(), $this->encodeUserPasswordPAP($requestPacket, $rawAttribute->getValue()));
    }

    /**
     * @inheritDoc
     */
    public function serializeValue(AttributeInterface $attribute, RequestPacket $requestPacket)
    {
        return $this->encodeUserPasswordPAP($requestPacket, $attribute->getValue());
    }

    /**
     * @param RequestPacket $requestPacket
     * @param string $value
     * @return string
     * @see https://tools.ietf.org/html/rfc2865#section-5.2
     */
    protected function encodeUserPasswordPAP(RequestPacket $requestPacket, string $value)
    {
        $result = '';
        $salt = $requestPacket->getAuthenticator();
        foreach (str_split($value, 16) as $chunk) {
            $v = md5($this->psk . $salt, true);
            $result .= $chunk ^ $v; // XOR
            $salt = $chunk;
        }
        return rtrim($result, "\0");
    }


}