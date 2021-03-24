<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Packet;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;

class ResponsePacket extends Packet
{

    /**
     * @param int $type
     * @return ResponsePacket
     */
    public function setType(int $type): ResponsePacket
    {
        $this->type = $type;
        return $this;
    }

}