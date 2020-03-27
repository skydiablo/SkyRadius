<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\AttributeHandler;

use SkyDiablo\SkyRadius\AttributeHandler\AttributeHandlerInterface;
use SkyDiablo\SkyRadius\Helper\UnPackInteger;

abstract class AbstractAttributeHandler implements AttributeHandlerInterface
{
    use UnPackInteger;
}