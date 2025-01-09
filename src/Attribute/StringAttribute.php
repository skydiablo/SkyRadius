<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

use SkyDiablo\SkyRadius\Exception\RangeException;

/**
 * Class StringAttribute
 * @package SkyDiablo\SkyRadius\Attribute
 * @method getValue() string
 */
class StringAttribute extends AbstractAttribute
{
    private const MAX_LENGTH = 253;  // RADIUS attribute value max length

    /**
     * StringAttribute constructor.
     * @param int $type
     * @param string $value
     */
    public function __construct(int $type, string $value)
    {
        $this->validateLength($value);
        parent::__construct($type, $value);
    }

    /**
     * Validates the length of the string value according to RADIUS protocol
     * @param string $value
     * @throws RangeException
     */
    private function validateLength(string $value): void
    {
        if (strlen($value) > self::MAX_LENGTH) {
            throw new RangeException(
                sprintf('Value length %d exceeds maximum allowed length of %d bytes', strlen($value), self::MAX_LENGTH)
            );
        }
    }

}