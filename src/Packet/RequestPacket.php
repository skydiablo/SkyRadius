<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Packet;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;

class RequestPacket extends Packet
{
    /**
     * @var int
     */
    private $identifier;

    /**
     * @var string
     */
    private $authenticator;

    /**
     * Packet constructor.
     * @param int $type
     * @param int $identifier
     * @param string $authenticator
     */
    public function __construct(int $type, int $identifier, string $authenticator)
    {
        parent::__construct($type);
        $this->identifier = $identifier;
        $this->authenticator = $authenticator;
    }

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getAuthenticator(): string
    {
        return $this->authenticator;
    }

}