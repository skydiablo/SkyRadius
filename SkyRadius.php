<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius;

use SkyDiablo\SkyRadius\AttributeHandler\RawAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\VendorSpecificAttributeHandler;
use SkyDiablo\SkyRadius\Connection\Context;
use Evenement\EventEmitter;
use React\Datagram\Factory;
use React\Datagram\Socket;
use React\EventLoop\LoopInterface;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\AttributeHandler\AttributeHandlerInterface;
use SkyDiablo\SkyRadius\AttributeHandler\ChapPasswordAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IntegerAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\UserPasswordAttributeHandler;
use SkyDiablo\SkyRadius\Packet\RequestPacket;
use SkyDiablo\SkyRadius\Packet\ResponsePacket;
use SkyDiablo\SkyRadius\Helper\UnPackInteger;

class SkyRadius extends EventEmitter
{

    use UnPackInteger;

    const EVENT_PACKET = 'packet';
    const EVENT_SERVER_READY = 'server-ready';

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var string
     */
    private $psk;

    /**
     * @var AttributeManager
     */
    protected $attributeHandler;

    /**
     * @var RawAttributeHandler
     */
    protected $rawAttributeHandler;

    /**
     * SkyRadius constructor.
     * @param LoopInterface $loop
     * @param string $uri udp://0.0.0.0:3400 listen on all interfaces at port 3400
     * @param string $psk
     * @param AttributeManager|null $attributeHandler
     */
    public function __construct(LoopInterface $loop, string $uri, string $psk, AttributeManager $attributeHandler = null)
    {
        $this->loop = $loop;
        $this->psk = $psk;
        $this->attributeHandler = $attributeHandler ?: new AttributeManager();
        $this->rawAttributeHandler = new RawAttributeHandler();
        $this->initRFC2865AttributeHandler();

        $this->socket = (new Factory($loop))->createServer($uri);

        $this->socket
            ->then(function (Socket $socket) {
                $socket->on('message', function (string $raw, string $peer, Socket $server) {
                    $context = new Context($this->handleRawInput($raw));
                    $this->emit(self::EVENT_PACKET, [$context]);
                    $this->sendResponse($context, $peer, $server);
                });
            })
            ->then(function (Socket $socket) {
                $this->emit(self::EVENT_SERVER_READY, [$socket, $this]);
            })
            ->otherwise(function (\Throwable $e) {
                $this->emit('error', [$e]);
            });
    }

    protected function initRFC2865AttributeHandler()
    {
        $this->attributeHandler
            ->setHandler(new VendorSpecificAttributeHandler(), AttributeInterface::ATTR_VENDOR_SPECIFIC)
            ->setHandler(
                new UserPasswordAttributeHandler(),
                AttributeInterface::ATTR_USER_PASSWORD,
                AttributeInterface::ATTR_TYPE_ALIAS[AttributeInterface::ATTR_USER_PASSWORD]
            )->setHandler(
                new ChapPasswordAttributeHandler(),
                AttributeInterface::ATTR_CHAP_PASSWORD,
                AttributeInterface::ATTR_TYPE_ALIAS[AttributeInterface::ATTR_CHAP_PASSWORD]
            );

        $initHandlerFunction = function (AttributeHandlerInterface $handler, array $types) {
            foreach ($types as $type) {
                $this->setHandler(
                    $handler,
                    $type,
                    AttributeInterface::ATTR_TYPE_ALIAS[$type] ?? null
                );
            }
        };

        $initHandlerFunction(new StringAttributeHandler(), [
            AttributeInterface::ATTR_USER_NAME, AttributeInterface::ATTR_FILTER_ID, AttributeInterface::ATTR_REPLY_MESSAGE,
            AttributeInterface::ATTR_CALLBACK_NUMBER, AttributeInterface::ATTR_CALLBACK_ID, AttributeInterface::ATTR_FRAMED_ROUTE,
            AttributeInterface::ATTR_STATE, AttributeInterface::ATTR_CLASS, AttributeInterface::ATTR_CALLED_STATION_ID,
            AttributeInterface::ATTR_CALLING_STATION_ID, AttributeInterface::ATTR_NAS_IDENTIFIER, AttributeInterface::ATTR_PROXY_STATE,
            AttributeInterface::ATTR_LOGIN_LAT_SERVICE, AttributeInterface::ATTR_LOGIN_LAT_NODE, AttributeInterface::ATTR_LOGIN_LAT_GROUP,
            AttributeInterface::ATTR_FRAMED_APPLETALK_ZONE, AttributeInterface::ATTR_CHAP_CHALLENGE, AttributeInterface::ATTR_LOGIN_LAT_PORT,
        ]);

        $initHandlerFunction(new IntegerAttributeHandler(), [
            AttributeInterface::ATTR_NAS_PORT, AttributeInterface::ATTR_SERVICE_TYPE, AttributeInterface::ATTR_FRAMED_PROTOCOL,
            AttributeInterface::ATTR_FRAMED_ROUTING, AttributeInterface::ATTR_FRAMED_MTU, AttributeInterface::ATTR_FRAMED_COMPRESSION,
            AttributeInterface::ATTR_LOGIN_SERVICE, AttributeInterface::ATTR_LOGIN_TCP_PORT, AttributeInterface::ATTR_FRAMED_IPX_NETWORK,
            AttributeInterface::ATTR_SESSION_TIMEOUT, AttributeInterface::ATTR_IDLE_TIMEOUT, AttributeInterface::ATTR_TERMINATION_ACTION,
            AttributeInterface::ATTR_FRAMED_APPLETALK_LINK, AttributeInterface::ATTR_FRAMED_APPLETALK_NETWORK, AttributeInterface::ATTR_NAS_PORT_TYPE,
            AttributeInterface::ATTR_PORT_LIMIT,
        ]);

        $initHandlerFunction(new IPv4AttributeHandler(), [
            AttributeInterface::ATTR_NAS_IP_ADDRESS, AttributeInterface::ATTR_FRAMED_IP_ADDRESS, AttributeInterface::ATTR_FRAMED_IP_NETMASK,
            AttributeInterface::ATTR_LOGIN_IP_HOST,
        ]);
    }


    /**
     * @param AttributeHandlerInterface $handler
     * @param int $type
     * @param string|null $alias
     * @param array $values
     * @return $this
     */
    public function setHandler(AttributeHandlerInterface $handler, int $type, string $alias = null, array $values = [])
    {
        $this->attributeHandler->setHandler($handler, $type, $alias, $values);
        return $this;
    }

    /**
     * @param int $vendorId
     * @param AttributeHandlerInterface $handler
     * @param int $type
     * @param string|null $alias
     * @param array $values
     * @return $this
     */
    public function setVsaHandler(int $vendorId, AttributeHandlerInterface $handler, int $type, string $alias = null, array $values = [])
    {
        /** @var VendorSpecificAttributeHandler $vsaHandler */
        $vsaHandler = $this->attributeHandler->getHandler(AttributeInterface::ATTR_VENDOR_SPECIFIC);
        if ($vsaHandler) {
            $vsaHandler->setHandler($vendorId, $handler, $type, $alias, $values);
        } else {
            throw new \RuntimeException('VSA-Handler not set!');
        }
        return $this;
    }

    /**
     * @param Context $context
     * @param string $peer
     * @param Socket $socket
     */
    protected function sendResponse(Context $context, string $peer, Socket $socket)
    {
        $socket->send(
            $this->serializeResponse(
                $context->getResponse(),
                $context->getRequest()
            ),
            $peer
        );
    }

    /**
     * @param string $data
     * @return RequestPacket
     * @todo: the input buffer is set to 65536 bytes (64MB) by default, this should be enough for now.
     * @todo: you can change this value by "$this->socket->bufferSize". if this value is to small, this function have to
     * @todo: handle multiple input raw-input-data for the same request!
     */
    protected function handleRawInput(string $data): RequestPacket
    {
        $type = ord($data{0});
        $id = ord($data{1});
        $len = unpack('n', substr($data, 2, 2))[1];
        $authenticator = substr($data, 4, 16);
        $realDataLen = strlen($data);
        $pos = 20; // $type = 1byte + $id = 1byte + $len = 2byte + $authenticator = 16byte

        $requestPacket = new RequestPacket($type, $id, $authenticator, $this->psk);

        while (($pos < $len) && ($pos < $realDataLen)) {
            $rawAttr = $this->rawAttributeHandler->parseRawAttribute($data, $pos);
            $pos += $rawAttr->getAttributeLength();
            if ($attribute = $this->attributeHandler->deserializeRawAttribute($rawAttr, $requestPacket)) {
                $requestPacket->addAttribute($attribute);
            }
        }

        return $requestPacket;
    }

    /**
     * @param ResponsePacket $responsePacket
     * @param RequestPacket $requestPacket
     * @return string
     * @see https://tools.ietf.org/html/rfc2865#section-3
     */
    protected function serializeResponse(ResponsePacket $responsePacket, RequestPacket $requestPacket)
    {
        $attributesData = '';
        foreach ($responsePacket->getAttributes() as $attribute) {
            $attributesData .= $this->attributeHandler->serializeAttribute($attribute);
        }
        $haystack = $responsePacket->getType() .
            $requestPacket->getIdentifier() .
            $requestPacket->getAuthenticator() .
            $attributesData .
            $this->psk;
        $responseAuth = md5($haystack, true);

        $header = $this->packInt8($responsePacket->getType());
        $header .= $this->packInt8($requestPacket->getIdentifier());
        $length = strlen($header) + strlen($responseAuth) + strlen($attributesData) + 2; // +2 = 16bit/2byte int for "length" itself
        $lenAsBytes = $this->packInt16($length);

        return $header . $lenAsBytes . $responseAuth . $attributesData;
    }

}