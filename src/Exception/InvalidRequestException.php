<?php
declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Exception;

class InvalidRequestException extends SkyRadiusException
{
    public static function create(string $message = 'Invalid Request Exception', int $code = 0, \Throwable $previous = null)
    {
        return new self($message, $code, $previous);
    }
}