<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

/**
 * Class AbstractAttribute
 * @package SkyDiablo\SkyRadius\Attribute
 */
abstract class AbstractAttribute implements AttributeInterface
{

    /**
     * @var int
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $typeAlias;

    /**
     * @var string
     */
    private $valueAlias;

    /**
     * RadiusAttribute constructor.
     * @param int $type
     * @param mixed $value
     */
    public function __construct(int $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getTypeAlias(): ?string
    {
        return $this->typeAlias;
    }

    /**
     * @param string $typeAlias
     * @return AbstractAttribute
     */
    public function setTypeAlias(string $typeAlias): AbstractAttribute
    {
        $this->typeAlias = $typeAlias;
        return $this;
    }

    /**
     * @return string
     */
    public function getValueAlias(): ?string
    {
        return $this->valueAlias;
    }

    /**
     * @param string $valueAlias
     * @return AbstractAttribute
     */
    public function setValueAlias(string $valueAlias): AbstractAttribute
    {
        $this->valueAlias = $valueAlias;
        return $this;
    }

}