<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

use SkyDiablo\SkyRadius\Exception\InvalidArgumentException;

/**
 * Class StringAttribute
 * @package SkyDiablo\SkyRadius\Attribute
 * @method getValue() string
 */
class IPv4Attribute extends StringAttribute
{

    /**
     * IPv4Attribute constructor.
     * @param int $type
     * @param string $value
     */
    public function __construct(int $type, string $value)
    {
        $this->validateIPv4($value);
        parent::__construct($type, $value);
    }

    /**
     * Validates if the given value is a valid IPv4 address
     * @param string $value
     * @throws \InvalidArgumentException
     */
    private function validateIPv4(string $value): void 
    {
        if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidArgumentException(
                sprintf('Invalid IPv4 address: %s', $value)
            );
        }
    }
}
