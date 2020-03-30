<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius\Attribute;

class VendorSpecificAttribute implements AttributeInterface
{

    /**
     * @var int
     */
    private $vendorId;

    /**
     * @var AttributeInterface
     */
    private $innerVSA;

    /**
     * VendorSpecificAttribute constructor.
     * @param int $vendorId
     * @param AttributeInterface $vsa
     */
    public function __construct(int $vendorId, AttributeInterface $vsa)
    {
        $this->innerVSA = $vsa;
        $this->vendorId = $vendorId;
    }

    /**
     * @return int
     */
    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    /**
     * @return AttributeInterface
     */
    public function getInnerVSA(): AttributeInterface
    {
        return $this->innerVSA;
    }

    /**
     * @inheritDoc
     */
    public function getType(): int
    {
        return $this->innerVSA->getType();
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->innerVSA->getValue();
    }

    /**
     * @inheritDoc
     */
    public function setTypeAlias(string $alias)
    {
        return $this->innerVSA->setTypeAlias($alias);
    }

    /**
     * @inheritDoc
     */
    public function setValueAlias(string $alias)
    {
        return $this->innerVSA->setValueAlias($alias);
    }

    /**
     * @inheritDoc
     */
    public function getTypeAlias()
    {
        return $this->innerVSA->getTypeAlias();
    }

    /**
     * @inheritDoc
     */
    public function getValueAlias()
    {
        return $this->innerVSA->getValueAlias();
    }
}