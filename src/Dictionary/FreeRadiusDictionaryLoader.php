<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius\Dictionary;


use SkyDiablo\SkyRadius\AttributeHandler\IntegerAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\SkyRadius;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FreeRadiusDictionaryLoader
 * @package App\lib\SkyDiablo\SkyRadius\src\Dictionary\freeRADIUS
 * @see https://freeradius.org/radiusd/man/dictionary.html
 */
class FreeRadiusDictionaryLoader
{
    const MATCH_KEY = 'KEY';
    const MATCH_NAME = 'NAME';
    const MATCH_LOAD = 'LOAD';
    const MATCH_NUMBER = 'NUMBER';
    const MATCH_OID = 'OID';
    const MATCH_TYPE = 'TYPE';
    const MATCH_VALUE = 'VALUE';
    const MATCH_VALUE_NAME = 'VALUE_NAME';
    const MATCH_VALUE_NUMBER = 'VALUE_NUMBER';
    const REGEX_RAW_LINE = '!^(?P<' . self::MATCH_KEY . '>[\w\-]+)[ \t]+(?P<' . self::MATCH_NAME . '>[\w\-]+)([ \t]+(?P<' . self::MATCH_LOAD . '>.*))?$!m';
    const REGEX_VENDOR = '!(?P<' . self::MATCH_NUMBER . '>\d+)!m'; //@todo: add format handling
    const REGEX_ATTRIBUTE = '!(?P<' . self::MATCH_OID . '>\d+)[ \t]+(?P<' . self::MATCH_TYPE . '>(\w+))!m'; //@todo: add flags
    const REGEX_VALUE = '!(?P<' . self::MATCH_VALUE_NAME . '>[\w\-]+)[ \t]+(?P<' . self::MATCH_VALUE_NUMBER . '>(\d+))!m';

    /**
     * @var SkyRadius
     */
    private $skyRadius;

    private $attributeHandlerCache = [];

    /**
     * FreeRadiusDictionaryLoader constructor.
     * @param SkyRadius $skyRadius
     */
    public function __construct(SkyRadius $skyRadius)
    {
        $this->skyRadius = $skyRadius;
    }

    /**
     * @param string $path
     * @throws \Exception
     */
    public function load(string $path)
    {
        $finder = new Finder();
        /** @var SplFileInfo $file */
        foreach ($finder->in($path)->files() as $file) {
            $this->loadFile($file->openFile());
        }
    }

    /**
     * @param \SplFileObject $file
     * @throws \Exception
     */
    protected function loadFile(\SplFileObject $file)
    {
        $vendorIds = [];
        $currentVendorId = null;
        $attributesByVendorId = [];

        while (!$file->eof()) {
            $line = trim($file->fgets());
            $matches = $subMatches = [];
            if (preg_match(self::REGEX_RAW_LINE, $line, $matches)) {
                switch (strtoupper($matches[self::MATCH_KEY])) {
                    case 'VENDOR': // VENDOR vendor-name number [format=...]
                        if (preg_match(self::REGEX_VENDOR, $matches[self::MATCH_LOAD], $subMatches)) {
                            $vendorIds[$matches[self::MATCH_NAME]] = (int)$subMatches[self::MATCH_NUMBER];
                        }
                        break;
                    case 'BEGIN-VENDOR': // BEGIN-VENDOR vendor-name
                        if (!($currentVendorId = $vendorIds[$matches[self::MATCH_NAME]] ?? null)) {
                            throw new \Exception(sprintf('Vendor "%s" not found, can not load freeRADIUS dictionary file: %s', $matches[self::MATCH_NAME], $file->getPathname()));
                        }
                        break;
                    case 'END_VENDOR':
                        $currentVendorId = null;
                        break;
                    case 'ATTRIBUTE': // ATTRIBUTE name oid type [flags]
                        if (preg_match(self::REGEX_ATTRIBUTE, $matches[self::MATCH_LOAD], $subMatches)) {
                            $attributesByVendorId[$currentVendorId][$matches[self::MATCH_NAME]] ?? $attributesByVendorId[$currentVendorId][$matches[self::MATCH_NAME]] = [];
                            $attributesByVendorId[$currentVendorId][$matches[self::MATCH_NAME]] += [
                                self::MATCH_OID => (int)$subMatches[self::MATCH_OID],
                                self::MATCH_TYPE => $subMatches[self::MATCH_TYPE],
                            ];
                        }
                        break;
                    case 'VALUE': // VALUE attribute-name value-name number
                        if (preg_match(self::REGEX_VALUE, $matches[self::MATCH_LOAD], $subMatches)) {
                            $attributesByVendorId[$currentVendorId][$matches[self::MATCH_NAME]][self::MATCH_VALUE] ?? $attributesByVendorId[$currentVendorId][$matches[self::MATCH_NAME]][self::MATCH_VALUE] = [];
                            $attributesByVendorId[$currentVendorId][$matches[self::MATCH_NAME]][self::MATCH_VALUE] += [
                                (int)$subMatches[self::MATCH_VALUE_NUMBER] => $subMatches[self::MATCH_VALUE_NAME]
                            ];
                        }
                        break;
                    case '$INCLUDE': // $INCLUDE filename
                        $this->loadFile(new \SplFileObject($matches[self::MATCH_NAME]));
                        break;
                }
            }

        }

        foreach ($vendorIds as $vendorName => $vendorId) {
            foreach ($attributesByVendorId[$vendorId] as $attrName => $attr) {
                $this->skyRadius->setVsaHandler(
                    $vendorId,
                    $this->getAttributeHandlerByType($attr[self::MATCH_TYPE]),
                    $attr[self::MATCH_OID],
                    $attrName,
                    $attr[self::MATCH_VALUE] ?? []
                );
            }
        }

        foreach ($attributesByVendorId[null] ?? [] as $attrName => $attr) {
            $this->skyRadius->setHandler(
                $this->getAttributeHandlerByType($attr[self::MATCH_TYPE]),
                $attr[self::MATCH_OID],
                $attrName,
                $attr[self::MATCH_VALUE] ?? []
            );
        }

    }

    /**
     * @param string $type
     * @return mixed|IntegerAttributeHandler|IPv4AttributeHandler|StringAttributeHandler
     * @throws \Exception
     */
    public function getAttributeHandlerByType(string $type)
    {
        switch ($type = strtoupper($type)) {
            case 'STRING': // string       UTF-8 printable text (the RFCs call this "text")
            case 'OCTETS': // octets       opaque binary data (the RFCs call this "string")
                return $this->attributeHandlerCache[$type] ?? $this->attributeHandlerCache[$type] = new StringAttributeHandler();
                break;
            case 'IPADDR': // ipaddr       IPv4 address
                return $this->attributeHandlerCache[$type] ?? $this->attributeHandlerCache[$type] = new IPv4AttributeHandler();
                break;
            case 'BYTE': // byte         8-bit unsigned integer
            case 'SHORT': // short        16-bit unsigned integer
            case 'INTEGER': // integer      32-bit unsigned integer
            case 'INTEGER64': // integer64    64-bit unsigned integer
                return $this->attributeHandlerCache[$type] ?? $this->attributeHandlerCache[$type] = new IntegerAttributeHandler();
                break;
            default:
                // date         Seconds since January 1, 1970 (32-bits)
                // ipv6addr     IPv6 Address
                // ipv6prefix   IPV6 prefix, with mask
                // ifid         Interface Id (hex:hex:hex:hex)
                // ether        Ethernet MAC address
                // abinary      Ascend binary filter format
                // signed       31 - bit signed integer(packed into 32 - bit field)
                // tlv          Type - Length - Value(allows nested attributes)
                // ipv4prefix   IPv4 Prefix as given in RFC 6572.
                throw new \Exception(sprintf('Type "%s" not implemented, yet', $type));
                break;
        }
    }

}