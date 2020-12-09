<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Exception;

/**
 * Class SilentDiscardPackageException
 * @package SkyDiablo\SkyRadius\Exception
 */
class InvalidResponseException extends SkyRadiusException
{

    public static function create(string $message = 'Invalid Response Exception', int $code = 0, \Throwable $previous = null)
    {
        return new self($message, $code, $previous);
    }

}