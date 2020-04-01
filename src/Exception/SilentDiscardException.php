<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Exception;

/**
 * Class SilentDiscardPackageException
 * @package App\lib\SkyDiablo\SkyRadius\Exception
 */
class SilentDiscardException extends SkyRadiusException
{

    public static function create(string $message = 'Silent Discard Exception', int $code = 0, \Throwable $previous = null)
    {
        return new self($message, $code, $previous);
    }

}