<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\RawAttribute;
use SkyDiablo\SkyRadius\Attribute\VendorSpecificAttribute;
use SkyDiablo\SkyRadius\AttributeHandler\AttributeHandlerInterface;
use SkyDiablo\SkyRadius\Helper\UnPackInteger;
use SkyDiablo\SkyRadius\Packet\Packet;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\Packet\RequestPacket;

class AttributeManager
{

    use UnPackInteger;

    const HANDLER = 0;
    const TYPE_ALIAS = 1;
    const VALUE_ALIAS = 2;

    /**
     * @var AttributeHandlerInterface[]
     */
    private array $handler;

    /**
     * @param AttributeHandlerInterface $handler
     * @param int $type
     * @param string|null $alias
     * @param array $values
     * @return AttributeManager
     */
    public function setHandler(AttributeHandlerInterface $handler, int $type, string $alias = null, array $values = []): AttributeManager
    {
        //@todo: this is an "easy way" to store handler and other stuff, maybe we should use an ValueObject?!
        $this->handler[$type] = [
            self::HANDLER => $handler,
            self::TYPE_ALIAS => $alias,
            self::VALUE_ALIAS => $values
        ];
        return $this;
    }

    /**
     * @param int $type
     * @return AttributeHandlerInterface|null
     */
    public function getHandler(int $type): ?AttributeHandlerInterface
    {
        return $this->handler[$type][self::HANDLER] ?? null;
    }

    /**
     * @param RawAttribute $rawAttribute
     * @param PacketInterface $requestPacket
     * @return AttributeInterface
     */
    public function deserializeRawAttribute(RawAttribute $rawAttribute, PacketInterface $requestPacket): ?AttributeInterface
    {
        $handlerConf = $this->handler[$rawAttribute->getType()] ?? null;
        if ($handlerConf) {
            /** @var AttributeHandlerInterface $handler */
            $handler = $handlerConf[self::HANDLER];
            $attr = $handler->deserializeRawAttribute($rawAttribute, $requestPacket);
            if ($attr) {
                /** @var string $alias */
                if ($alias = $handlerConf[self::TYPE_ALIAS] ?? null) {
                    $attr->setTypeAlias($alias);
                }
                if ($alias = $handlerConf[self::VALUE_ALIAS][$attr->getValue()] ?? null) {
                    $attr->setValueAlias($alias);
                }
                return $attr;
            }
        }
        return null;
    }

    /**
     * @param AttributeInterface $attribute
     * @param int $type
     * @param RequestPacket $requestPacket
     * @return string
     */
    protected function serializeValueByType(AttributeInterface $attribute, int $type, RequestPacket $requestPacket): ?string
    {
        $handlerConf = $this->handler[$type] ?? null;
        if ($handlerConf) {
            /** @var AttributeHandlerInterface $handler */
            $handler = $handlerConf[self::HANDLER];
            return $handler->serializeValue($attribute, $requestPacket);
        }
        return null;
    }

    /**
     * @param AttributeInterface $attribute
     * @param RequestPacket $requestPacket
     * @return string|null
     */
    public function serializeAttribute(AttributeInterface $attribute, RequestPacket $requestPacket): ?string
    {
        if ($attribute instanceof VendorSpecificAttribute) { //@todo: i hate to handle that in this way -.-
            $type = AttributeInterface::ATTR_VENDOR_SPECIFIC;
        } else {
            $type = $attribute->getType();
        }
        $attrRawValue = $this->serializeValueByType($attribute, $type, $requestPacket); //@todo: max value length should must be 253 bytes!
        if ($attrRawValue) {
            return $this->packInt8($type) .
                $this->packInt8(strlen($attrRawValue) + 2) . // +2 => type + length
                $attrRawValue;
        }
        return null;
    }
}