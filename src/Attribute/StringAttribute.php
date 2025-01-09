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
        if (strlen($value) > 253) {
            throw new RangeException(
                sprintf('Value length %d exceeds maximum allowed length of 253 bytes', strlen($value))
            );
        }
    }

}