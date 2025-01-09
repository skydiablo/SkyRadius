<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

use SkyDiablo\SkyRadius\Exception\RangeException;

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
     * @param int $value
     * @param int $bit
     * @throws \InvalidArgumentException
     */
    private function validateValueForBitSize(int $value, int $bit): void
    {
        $maxValue = (2 ** $bit) - 1;
        if ($value < 0 || $value > $maxValue) {
            throw new RangeException(
                sprintf('Value %d is outside valid range for %d-bit (0 to %d)',
                    $value, $bit, $maxValue)
            );
        }
    }

    /**
     * StringAttribute constructor.
     * @param int $type
     * @param int $value
     * @param int $bit
     */
    public function __construct(int $type, int $value, int $bit = self::BIT_32)
    {
        assert(in_array($bit, self::FORMATTER));
        $this->validateValueForBitSize($value, $bit);
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