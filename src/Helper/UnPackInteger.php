<?php


namespace SkyDiablo\SkyRadius\Helper;

use SkyDiablo\SkyRadius\Attribute\IntegerAttribute;

trait UnPackInteger
{
    /**
     * @param int $bit
     * @param string $data
     * @param int $startPos
     * @return int
     */
    private function unpackInt(int $bit, string $data, $startPos = 0): int
    {
        $format = IntegerAttribute::FORMATTER[$bit];
        return unpack($format, substr($data, $startPos, $bit / 8))[1];
    }

    /**
     * @param $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt8($data, $startPos = 0): int
    {
        return $this->unpackInt(IntegerAttribute::BIT_8, $data, $startPos);
    }

    /**
     * @param $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt16($data, $startPos = 0): int
    {
        return $this->unpackInt(IntegerAttribute::BIT_16, $data, $startPos);
    }

    /**
     * @param $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt32($data, $startPos = 0): int
    {
        return $this->unpackInt(IntegerAttribute::BIT_32, $data, $startPos);
    }

    /**
     * @param $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt64($data, $startPos = 0): int
    {
        return $this->unpackInt(IntegerAttribute::BIT_64, $data, $startPos);
    }

    /**
     * @param int $bit
     * @param int $integer
     * @return string
     */
    private function packInt(int $bit, int $integer): string
    {
        $format = IntegerAttribute::FORMATTER[$bit];
        return pack($format, $integer);
    }

    /**
     * @param $integer
     * @return string
     */
    protected function packInt8(int $integer): string
    {
        return $this->packInt(IntegerAttribute::BIT_8, $integer);
    }

    /**
     * @param int $integer
     * @return string
     */
    protected function packInt16(int $integer): string
    {
        return $this->packInt(IntegerAttribute::BIT_16, $integer);
    }

    /**
     * @param $integer
     * @return string
     */
    protected function packInt32(int $integer): string
    {
        return $this->packInt(IntegerAttribute::BIT_32, $integer);
    }

    /**
     * @param $integer
     * @return string
     */
    protected function packInt64(int $integer): string
    {
        return $this->packInt(IntegerAttribute::BIT_64, $integer);
    }

}