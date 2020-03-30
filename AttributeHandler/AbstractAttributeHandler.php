<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\Helper\UnPackInteger;

/**
 * Class AbstractAttributeHandler
 * @package SkyDiablo\SkyRadius\AttributeHandler
 */
abstract class AbstractAttributeHandler implements AttributeHandlerInterface
{
    use UnPackInteger;
}