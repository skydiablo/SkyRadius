<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Exception;

use SkyDiablo\SkyRadius\Connection\Context;

class InvalidServerResponseException extends InvalidResponseException
{

    protected ?Context $context = null;

    /**
     * @param Context $context
     */
    protected function __construct(string $message = 'Invalid Response Exception', int $code = 0, \Throwable $previous = null, Context $context = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }


    public static function create(string $message = 'Invalid Response Exception', int $code = 0, \Throwable $previous = null, Context $context = null): self
    {
        return new self($message, $code, $previous, $context);
    }

    public function getContext(): Context
    {
        return $this->context;
    }

}