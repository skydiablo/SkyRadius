<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Packet;

use SkyDiablo\SkyRadius\Attribute\AttributeInterface;

interface PacketInterface
{

    const NOOB = 0;
    const ACCESS_REQUEST = 1;
    const ACCESS_ACCEPT = 2;
    const ACCESS_REJECT = 3;
    const ACCOUNTING_REQUEST = 4;
    const ACCOUNTING_RESPONSE = 5;
    const ACCESS_CHALLENGE = 11;
    const STATUS_SERVER = 12;
    const STATUS_CLIENT = 13;
    const RESERVED = 255;

    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @return AttributeInterface[]|AttributeInterface[][]
     */
    public function getAttributes(): array;

    /**
     * @param int $type
     * @return AttributeInterface|AttributeInterface[]
     */
    public function getAttributeByType(int ...$type);

    /**
     * @param string $alias
     * @return AttributeInterface[]
     */
    public function getAttributeByAlias(string ...$alias);

    /**
     * @return string
     */
    public function getAuthenticator(): string;

    /**
     * @return int
     */
    public function getIdentifier(): int;


}