<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

/**
 * Class TunnelTypeAttribute
 * @package SkyDiablo\SkyRadius\src\Attribute
 */
class TunnelAttribute extends AbstractAttribute
{
    /**
     * Valid values are 1 - 31 (0x01 - 0x1F), otherwise this value should ignored
     * @var int
     */
    private int $tag;

    public function __construct(int $type, int $tag, $value)
    {
        parent::__construct($type, $value);
        $this->tag = $tag;
    }

    /**
     * @return int
     */
    public function getTag(): int
    {
        return $this->tag;
    }

}