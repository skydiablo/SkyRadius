<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius;

use SkyDiablo\SkyRadius\Exception\InvalidResponseException;
use SkyDiablo\SkyRadius\Exception\SilentDiscardException;
use SkyDiablo\SkyRadius\AttributeHandler\TunnelPasswordAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\RawAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\Tunnel3ByteValueAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\TunnelAttributeHandler;
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
use SkyDiablo\SkyRadius\Exception\SkyRadiusException;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\Packet\RequestPacket;
use SkyDiablo\SkyRadius\Packet\ResponsePacket;
use SkyDiablo\SkyRadius\Helper\UnPackInteger;

class SkyRadius extends EventEmitter
{

    use UnPackInteger;

    const EVENT_PACKET = 'packet';
    const EVENT_SERVER_READY = 'server-ready';
    const EVENT_ERROR = 'error';

    /**
     * @var LoopInterface
     */
    private LoopInterface $loop;

    /**
     * @var string
     */
    private string $psk;

    /**
     * @var AttributeManager
     */
    protected AttributeManager $attributeManager;

    /**
     * @var RawAttributeHandler
     */
    protected RawAttributeHandler $rawAttributeHandler;

    /**
     * SkyRadius constructor.
     * @param LoopInterface $loop
     * @param string $uri udp://0.0.0.0:3400 listen on all interfaces at port 3400
     * @param string $psk
     * @param AttributeManager|null $attributeManager
     */
    public function __construct(LoopInterface $loop, string $uri, string $psk, AttributeManager $attributeManager = null)
    {
        $this->loop = $loop;
        $this->psk = $psk;
        $this->attributeManager = $attributeManager ?: new AttributeManager();
        $this->rawAttributeHandler = new RawAttributeHandler();
        $this->initRFCAttributeHandler();

        (new Factory($loop))->createServer($uri)
            ->then(function (Socket $socket) {
                $socket->on('message', function (string $raw, string $peer, Socket $server) {
                    try {
                        $context = new Context($this->handleRawInput($raw));
                        $this->emit(self::EVENT_PACKET, [$context]);
                        $this->sendResponse($context, $peer, $server);
                    } catch (SilentDiscardException $e) {
                        // silently ignored...
                        // @todo: logging?!
                    } catch (SkyRadiusException $e) {
                        $this->emit(self::EVENT_ERROR, [$e]);
                    }
                });
                return $socket;
            })
            ->then(function (Socket $socket) {
                $this->emit(self::EVENT_SERVER_READY, [$socket, $this]);
            })
            ->otherwise(function (\Throwable $e) {
                $this->emit('error', [$e]);
            });
    }

    protected function initRFCAttributeHandler()
    {
        $this->attributeManager
            ->setHandler(new VendorSpecificAttributeHandler(), AttributeInterface::ATTR_VENDOR_SPECIFIC);

        $initHandlerFunction = function (AttributeHandlerInterface $handler, array $types) {
            foreach ($types as $type) {
                $this->setHandler(
                    $handler,
                    $type,
                    AttributeInterface::ATTR_TYPE_ALIAS[$type] ?? null
                );
            }
        };

        // ======== RFC2865 =========

        $initHandlerFunction(new UserPasswordAttributeHandler($this->psk), [
            AttributeInterface::ATTR_USER_PASSWORD
        ]);

        $initHandlerFunction(new ChapPasswordAttributeHandler(), [
            AttributeInterface::ATTR_CHAP_PASSWORD
        ]);

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

        $initHandlerFunction(new TunnelAttributeHandler(), [
            AttributeInterface::ATTR_TUNNEL_CLIENT_ENDPOINT, AttributeInterface::ATTR_TUNNEL_SERVER_ENDPOINT, AttributeInterface::ATTR_TUNNEL_PRIVATE_GROUP_ID,
            AttributeInterface::ATTR_TUNNEL_ASSIGNMENT_ID, AttributeInterface::ATTR_TUNNEL_CLIENT_AUTH_ID, AttributeInterface::ATTR_TUNNEL_SERVER_AUTH_ID,
        ]);

        $initHandlerFunction(new Tunnel3ByteValueAttributeHandler(), [
            AttributeInterface::ATTR_TUNNEL_TYPE, AttributeInterface::ATTR_TUNNEL_MEDIUM_TYPE, AttributeInterface::ATTR_TUNNEL_PREFERENCE,
        ]);

        $initHandlerFunction(new TunnelPasswordAttributeHandler($this->psk), [
            AttributeInterface::ATTR_TUNNEL_PASSWORD,
        ]);

        // ======== RFC2866 =========

        $initHandlerFunction(new IntegerAttributeHandler(), [
            AttributeInterface::ATTR_ACCT_STATUS_TYPE,
            AttributeInterface::ATTR_ACCT_DELAY_TIME,
            AttributeInterface::ATTR_ACCT_INPUT_OCTETS,
            AttributeInterface::ATTR_ACCT_OUTPUT_OCTETS,
            AttributeInterface::ATTR_ACCT_AUTHENTIC,
            AttributeInterface::ATTR_ACCT_SESSION_TIME,
            AttributeInterface::ATTR_ACCT_INPUT_PACKETS,
            AttributeInterface::ATTR_ACCT_OUTPUT_PACKETS,
            AttributeInterface::ATTR_ACCT_TERMINATE_CAUSE,
            AttributeInterface::ATTR_ACCT_LINK_COUNT,
        ]);

        $initHandlerFunction(new StringAttributeHandler(), [
            AttributeInterface::ATTR_ACCT_SESSION_ID,
            AttributeInterface::ATTR_ACCT_MULTI_SESSION_ID,
        ]);


        // ============= RFC2869 ============

        $initHandlerFunction(new IntegerAttributeHandler(), [
            AttributeInterface::ATTR_ACCT_INPUT_GIGAWORDS,
            AttributeInterface::ATTR_ACCT_OUTPUT_GIGAWORDS,
            AttributeInterface::ATTR_EVENT_TIMESTAMP,
            AttributeInterface::ATTR_PASSWORD_RETRY,
            AttributeInterface::ATTR_PROMPT,
            AttributeInterface::ATTR_ACCT_INTERIM_INTERVAL,
        ]);

        $initHandlerFunction(new StringAttributeHandler(), [
            AttributeInterface::ATTR_CONNECT_INFO,
            AttributeInterface::ATTR_CONFIGURATION_TOKEN,
            AttributeInterface::ATTR_NAS_PORT_ID,
            AttributeInterface::ATTR_FRAMED_POOL,
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
        $this->attributeManager->setHandler($handler, $type, $alias, $values);
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
        $vsaHandler = $this->attributeManager->getHandler(AttributeInterface::ATTR_VENDOR_SPECIFIC);
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
     * @throws InvalidResponseException
     */
    protected function sendResponse(Context $context, string $peer, Socket $socket)
    {
        if (!$this->validateResponse($context)) {
            throw InvalidResponseException::create();
        }
        $socket->send(
            $this->serializeResponse(
                $context->getResponse(),
                $context->getRequest()
            ),
            $peer
        );
    }

    /**
     * @param Context $context
     * @return bool
     */
    protected function validateResponse(Context $context): bool
    {
        switch ($context->getRequest()->getType()) {
            case PacketInterface::ACCESS_REQUEST:
                return in_array($context->getResponse()->getType(), [
                    //an ACCESS-REQUEST must have an ACCESS-RESPONSE
                    PacketInterface::ACCESS_ACCEPT, PacketInterface::ACCESS_REJECT
                ], true);
            case PacketInterface::ACCOUNTING_REQUEST;
                //an ACCOUNTING-REQUEST must have an ACCOUNTING-RESPONSE
                return $context->getResponse()->getType() === PacketInterface::ACCOUNTING_RESPONSE;
            default:
                return true;
        }
    }

    /**
     * @param string $data
     * @return RequestPacket
     * @throws SilentDiscardException
     * @todo: you can change this value by "$this->socket->bufferSize". if this value is to small, this function have to
     * @todo: handle multiple input raw-input-data for the same request!
     * @todo: the input buffer is set to 65536 bytes (64MB) by default, this should be enough for now.
     */
    protected function handleRawInput(string $data): RequestPacket
    {
        $type = $this->unpackInt8($data, 0);
        $id = $this->unpackInt8($data, 1);
        $len = $this->unpackInt16($data, 2);
        $authenticator = substr($data, 4, 16);
        $realDataLen = strlen($data);
        $pos = 20; // $type = 1byte + $id = 1byte + $len = 2byte + $authenticator = 16byte

        /*
         * Octets outside the range of the Length field
         * MUST be treated as padding and ignored on reception.  If the
         * packet is shorter than the Length field indicates, it MUST be
         * silently discarded.  The minimum length is 20 and maximum length
         * is 4096.
         */
        if ($len > $realDataLen) { // package is to short
            throw SilentDiscardException::create();
        }

        $requestPacket = new RequestPacket($type, $id, $authenticator, $data);

        if (!$this->validateRequest($requestPacket)) {
            throw SilentDiscardException::create();
        }

        while ($pos < $len) {
            $rawAttr = $this->rawAttributeHandler->parseRawAttribute($data, $pos);
            $pos += $rawAttr->getAttributeLength();
            if ($attribute = $this->attributeManager->deserializeRawAttribute($rawAttr, $requestPacket)) {
                $requestPacket->addAttribute($attribute);
            } else {

            }
        }
        return $requestPacket;
    }

    /**
     * @param RequestPacket $requestPacket
     * @return bool
     */
    protected function validateRequest(RequestPacket $requestPacket): bool
    {
        switch ($requestPacket->getType()) {
            case PacketInterface::ACCOUNTING_REQUEST:
                /*
                The Request Authenticator field in Accounting-Request packets contains a
                one-way MD5 hash calculated over a stream of octets consisting of the
                Code + Identifier + Length + 16 zero octets + request attributes + shared secret
                (where + indicates concatenation).
                */
                $haystack = substr_replace($requestPacket->getRaw(), str_repeat(chr(0x00), 16), 4, 16);
                $md5 = md5($haystack . $this->psk, true);
                return $md5 === $requestPacket->getAuthenticator();
            default:
                return true;
        }
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
            $attributesData .= $this->attributeManager->serializeAttribute($attribute, $requestPacket);
        }
        $header = $this->packInt8($responsePacket->getType());
        $header .= $this->packInt8($requestPacket->getIdentifier());
        // +1 type as byte       \
        // +1 identifier as byte  \  = 20 byte
        // +16 = response-auth    /
        // +2 = "length" itself  /
        $header .= $this->packInt16(strlen($attributesData) + 20);
        $haystack =
            $header . // type + id + length
            $requestPacket->getAuthenticator() .
            $attributesData .
            $this->psk;
        $responseAuth = md5($haystack, true);


        return $header . $responseAuth . $attributesData;
    }

}