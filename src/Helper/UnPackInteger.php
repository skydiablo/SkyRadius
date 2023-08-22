<?php


namespace SkyDiablo\SkyRadius\Helper;

use InvalidArgumentException;
use SkyDiablo\SkyRadius\Attribute\IntegerAttribute;

trait UnPackInteger
{
    /**
     * @param int $bit
     * @param string $data
     * @param int $startPos
     * @return int
     * @throws InvalidArgumentException
     */
    private function unpackInt(int $bit, string $data, int $startPos = 0): int
    {
        if (!$data) {
            throw new InvalidArgumentException("Empty input data");
        }
        $format = IntegerAttribute::FORMATTER[$bit];
        return unpack($format, substr($data, $startPos, $bit / 8))[1];
    }

    /**
     * @param string $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt8(string $data, int $startPos = 0): int
    {
        return $this->unpackInt(IntegerAttribute::BIT_8, $data, $startPos);
    }

    /**
     * @param string $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt16(string $data, int $startPos = 0): int
    {
        return $this->unpackInt(IntegerAttribute::BIT_16, $data, $startPos);
    }

    /**
     * @param string $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt32(string $data, int $startPos = 0): int
    {
        return $this->unpackInt(IntegerAttribute::BIT_32, $data, $startPos);
    }

    /**
     * @param string $data
     * @param int $startPos
     * @return int
     */
    protected function unpackInt64(string $data, int $startPos = 0): int
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
        return $this->packIntByFormat($format, [$integer]);
    }

    /**
     * @param string $format
     * @param array<int> $integer
     * @return string
     */
    public function packIntByFormat(string $format, array $integer): string
    {
        return pack($format, ...$integer);
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
