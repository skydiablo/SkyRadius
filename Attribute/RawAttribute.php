<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

class RawAttribute extends AbstractAttribute
{

    /**
     * @var int
     */
    private $valueLength;

    /**
     * @var int
     */
    private $attributeLength;

    /**
     * RawAttribute constructor.
     * @param int $type
     * @param int $valueLength
     * @param string $value
     * @param int $attributeLength
     */
    public function __construct(int $type, int $valueLength, string $value, int $attributeLength)
    {
        parent::__construct($type, $value);
        $this->valueLength = $valueLength;
        $this->attributeLength = $attributeLength;
    }

    /**
     * @return int
     */
    public function getValueLength(): int
    {
        return $this->valueLength;
    }

    /**
     * @return int
     */
    public function getAttributeLength(): int
    {
        return $this->attributeLength;
    }

}