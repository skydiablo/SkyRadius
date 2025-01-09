<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

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
        parent::__construct($type, $value);
        assert(filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
    }
}
