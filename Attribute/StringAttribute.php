<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

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
        // by RADIUS protocol design, the length is limited to 253 bytes
        assert(strlen($value) <= 253);
        parent::__construct($type, $value);
    }

}