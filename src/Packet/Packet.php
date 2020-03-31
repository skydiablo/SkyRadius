<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Packet;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;

class Packet implements PacketInterface
{
    /**
     * @var int
     */
    protected $type;

    /**
     * @var \SplObjectStorage|AttributeInterface[]
     */
    private $attributes;

    /**
     * Packet constructor.
     * @param int $type
     */
    public function __construct(int $type)
    {
        $this->type = $type;
        $this->attributes = new \SplObjectStorage();
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
    public function addAttributes(array $attributes)
    {
        array_map([$this, 'addAttribute'], $attributes);
        return $this;
    }

    /**
     * @param AttributeInterface $attribute
     * @return Packet
     */
    public function addAttribute(AttributeInterface $attribute)
    {
        $this->attributes->attach($attribute, $attribute->getType());
        return $this;
    }

    /**
     * @param int $type
     * @return AttributeInterface[]
     */
    public function getAttributeByType(int ...$type)
    {
        return array_filter($this->attributes, function (AttributeInterface $attribute) use ($type) {
            return in_array($this->attributes[$attribute], $type, true);
        });
    }

    /**
     * @param string $alias
     * @return AttributeInterface[]
     */
    public function getAttributeByAlias(string ...$alias)
    {
        return array_filter($this->attributes, function (AttributeInterface $attribute) use ($alias) {
            return in_array($attribute->getTypeAlias() , $alias, true);
        });
    }

    /**
     * @param $identifier
     * @return AttributeInterface[]
     */
    public function getAttribute(...$identifier)
    {
        if (is_int($identifier)) {
            return $this->getAttributeByType(...$identifier);
        } else {
            return $this->getAttributeByAlias(...(string)$identifier);
        }
    }

}