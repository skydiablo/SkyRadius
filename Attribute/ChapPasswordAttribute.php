<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius\Attribute;

/**
 * Class ChapAttribute
 * @package SkyDiablo\SkyRadius\Attribute
 * @see https://tools.ietf.org/html/rfc2865#section-5.3
 * @method getValue() string
 */
class ChapPasswordAttribute extends StringAttribute
{

    /**
     * This field is one octet, and contains the CHAP Identifier from the user's CHAP Response.
     * @todo is this a simple byte or should it convert to an ordinary value ?
     * @var string
     */
    private $chapIdent;

    public function __construct(int $type, string $chapIdent, string $chapResponse)
    {
        parent::__construct($type, $chapResponse);
        $this->chapIdent = $chapIdent;
    }

    /**
     * @return string
     */
    public function getChapIdent(): string
    {
        return $this->chapIdent;
    }

    /**
     * @return string
     */
    public function getChapResponse(): string
    {
        return $this->getValue();
    }

}