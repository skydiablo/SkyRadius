<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

/**
 * Class StringAttribute
 * @package SkyDiablo\SkyRadius\Attribute
 * @method getValue() int
 */
class IntegerAttribute extends AbstractAttribute
{

    const BIT_8 = 8;
    const BIT_16 = 16;
    const BIT_32 = 32;
    const BIT_64 = 64;

    const FORMATTER = [
        self::BIT_8 => 'C',
        self::BIT_16 => 'n',
        self::BIT_32 => 'N',
        self::BIT_64 => 'J',
    ];

    private $bit;

    /**
     * StringAttribute constructor.
     * @param int $type
     * @param int $value
     * @param int $bit
     */
    public function __construct(int $type, int $value, int $bit = self::BIT_32)
    {
        parent::__construct($type, $value);
        $this->bit = $bit;
    }

    /**
     * @return int
     */
    public function getBit(): int
    {
        return $this->bit;
    }

}