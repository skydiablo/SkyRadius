<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Packet;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\SkyRadius;

class Packet implements PacketInterface
{
    /**
     * @var int
     */
    protected int $type;

    /**
     * @var int
     */
    private int $identifier;

    /**
     * @var string
     */
    private string $authenticator;

    /**
     * @var string
     */
    private string $raw;

    /**
     * @var \SplObjectStorage|AttributeInterface[]
     */
    private $attributes;

    /**
     * @var \SplObjectStorage|AttributeInterface[]
     */
    private $unknownRawAttributes;

    /**
     * Packet constructor.
     * @param int $type
     * @param int $identifier
     * @param string $authenticator
     * @param string $raw
     */
    public function __construct(int $type, int $identifier, string $authenticator, string $raw)
    {
        $this->type = $type;
        $this->identifier = $identifier;
        $this->authenticator = str_pad(substr($authenticator, 0, SkyRadius::AUTHENTICATOR_LENGTH), SkyRadius::AUTHENTICATOR_LENGTH, chr(0x00), STR_PAD_RIGHT);
        $this->raw = $raw;
        $this->attributes = new \SplObjectStorage();
        $this->unknownRawAttributes = new \SplObjectStorage();
    }

    /**
     * @param string $raw
     */
    public function setRaw(string $raw): Packet
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticator(): string
    {
        return $this->authenticator;
    }

    /**
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }


    /**
     * @return AttributeInterface[]
     */
    public function getAttributes(): array
    {
        return iterator_to_array($this->attributes, false);
    }

    /**
     * @param AttributeInterface[] $attributes
     * @return $this
     */
    public function addAttributes(array $attributes): Packet
    {
        array_map([$this, 'addAttribute'], $attributes);
        return $this;
    }

    /**
     * @param AttributeInterface $attribute
     * @return Packet
     */
    public function addAttribute(AttributeInterface $attribute): Packet
    {
        $this->attributes->attach($attribute, $attribute->getType());
        return $this;
    }

    /**
     * @param AttributeInterface $attribute
     * @return Packet
     */
    public function addUnknownRawAttribute(AttributeInterface $attribute): Packet
    {
        $this->unknownRawAttributes->attach($attribute, $attribute->getType());
        return $this;
    }

    /**
     * @return AttributeInterface[]
     */
    public function getUnknownRawAttributes(): array
    {
        return iterator_to_array($this->unknownRawAttributes, false);
    }

    /**
     * @param int $type
     * @return AttributeInterface[]
     */
    public function getAttributeByType(int ...$type): array
    {
        return array_filter($this->getAttributes(), function (AttributeInterface $attribute) use ($type) {
            return in_array($this->attributes[$attribute], $type, true);
        });
    }

    /**
     * @param AttributeInterface $attribute
     * @return Packet
     */
    public function removeAttribute(AttributeInterface $attribute): Packet
    {
        $this->attributes->detach($attribute);
        return $this;
    }

    /**
     * @param string $alias
     * @return AttributeInterface[]
     */
    public function getAttributeByAlias(string ...$alias): array
    {
        return array_filter($this->getAttributes(), function (AttributeInterface $attribute) use ($alias) {
            return in_array($attribute->getTypeAlias(), $alias, true);
        });
    }

    /**
     * @param $identifier
     * @return AttributeInterface[]
     */
    public function getAttribute(...$identifiers): array
    {
        $filterIntParams = function (array $params, bool $isInt = true) {
            return array_filter($params, function ($param) use ($isInt) {
                return is_int($param) ? $isInt : !$isInt;
            });
        };

        return array_merge(
            $this->getAttributeByType(...$filterIntParams($identifiers)),
            $this->getAttributeByAlias(...$filterIntParams($identifiers, false))
        );
    }

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

}