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
    private int $type;

    /**
     * @var mixed
     */
    private mixed $value;

    /**
     * @var ?string
     */
    private ?string $typeAlias = null;

    /**
     * @var ?string
     */
    private ?string $valueAlias = null;

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
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return ?string
     */
    public function getTypeAlias(): ?string
    {
        return $this->typeAlias;
    }

    /**
     * @param ?string $alias
     *
     * @return AbstractAttribute
     */
    public function setTypeAlias(?string $alias): AbstractAttribute
    {
        $this->typeAlias = $alias;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getValueAlias(): ?string
    {
        return $this->valueAlias;
    }

    /**
     * @param ?string $alias
     *
     * @return AbstractAttribute
     */
    public function setValueAlias(?string $alias): AbstractAttribute
    {
        $this->valueAlias = $alias;
        return $this;
    }

}