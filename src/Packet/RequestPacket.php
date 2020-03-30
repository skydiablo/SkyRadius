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
     * @var string
     */
    private $psk;

    /**
     * Packet constructor.
     * @param int $type
     * @param int $identifier
     * @param string $authenticator
     * @param string $psk
     */
    public function __construct(int $type, int $identifier, string $authenticator, string $psk)
    {
        parent::__construct($type);
        $this->identifier = $identifier;
        $this->authenticator = $authenticator;
        $this->psk = $psk;
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

    /**
     * @return string
     */
    public function getPsk(): string
    {
        return $this->psk;
    }

}