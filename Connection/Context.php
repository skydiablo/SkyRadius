<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Connection;

use SkyDiablo\SkyRadius\Packet\RequestPacket;
use SkyDiablo\SkyRadius\Packet\ResponsePacket;

/**
 * Class Context
 * @package SkyDiablo\SkyRadius\Connection
 */
class Context
{

    /**
     * @var RequestPacket
     */
    private $request;

    /**
     * @var ResponsePacket
     */
    private $response;

    /**
     * Context constructor.
     * @param RequestPacket $request
     */
    public function __construct(RequestPacket $request)
    {
        $this->request = $request;
        //by default, all requests are rejected!
        $this->response = new ResponsePacket(ResponsePacket::ACCESS_REJECT);
    }

    /**
     * @return RequestPacket
     */
    public function getRequest(): RequestPacket
    {
        return $this->request;
    }

    /**
     * @return ResponsePacket
     */
    public function getResponse(): ResponsePacket
    {
        return $this->response;
    }

}