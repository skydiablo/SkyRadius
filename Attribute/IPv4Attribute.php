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
    public function __construct(int $type, string $ip)
    {
        parent::__construct($type, $ip);
    }

}