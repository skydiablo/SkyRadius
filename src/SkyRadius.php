<?php

declare(strict_types=1);


namespace SkyDiablo\SkyRadius;

use React\EventLoop\Loop;
use SkyDiablo\SkyRadius\AttributeHandler\TunnelPasswordAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\RawAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\Tunnel3ByteValueAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\TunnelAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\VendorSpecificAttributeHandler;
use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\AttributeHandler\AttributeHandlerInterface;
use SkyDiablo\SkyRadius\AttributeHandler\ChapPasswordAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IntegerAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\StringAttributeHandler;
use SkyDiablo\SkyRadius\AttributeHandler\UserPasswordAttributeHandler;
use SkyDiablo\SkyRadius\Exception\SilentDiscardException;
use SkyDiablo\SkyRadius\Helper\UnPackInteger;
use SkyDiablo\SkyRadius\Packet\Packet;
use SkyDiablo\SkyRadius\Packet\RequestPacket;
use SkyDiablo\SkyRadius\Packet\ResponsePacket;

abstract class SkyRadius extends EventEmitter
{

    use UnPackInteger;

    const EVENT_PACKET = 'packet';
    const EVENT_ERROR = 'error';
    const EVENT_PACKET_DISCARDED = 'packed-discarded';
    const RAW_INPUT_TYPE_REQUEST = 'request';
    const RAW_INPUT_TYPE_RESPONSE = 'response';
    const AUTHENTICATOR_LENGTH = 16;

    /**
     * @var LoopInterface
     */
    protected LoopInterface $loop;

    /**
     * @var string
     */
    protected string $psk;

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
     * @param string $uri udp://0.0.0.0:3400 listen at all interfaces on port 3400
     * @param string $psk
     * @param AttributeManager|null $attributeManager
     * @param LoopInterface|null $loop
     */
    public function __construct(string $uri, string $psk, AttributeManager $attributeManager = null, LoopInterface $loop = null)
    {
        $this->loop = $loop ?? Loop::get();
        $this->psk = $psk;
        $this->attributeManager = $attributeManager ?: new AttributeManager();
        $this->rawAttributeHandler = new RawAttributeHandler();
        $this->initRFCAttributeHandler();
    }

    /**
     * @todo load from file?
     */
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
     * @param string $data
     * @param string $rawInputType
     * @return Packet
     * @throws SilentDiscardException
     */
    protected function handleRawInput(string $data, string $rawInputType): Packet
    {
        $data = trim($data, "\n");
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
            throw SilentDiscardException::create(sprintf('Request-Message is to short. HeaderLength: %d / RealLength: %d', $len, $realDataLen));
        } elseif ($realDataLen > 4096) { //todo: just trim?
            throw SilentDiscardException::create(sprintf('Request-Message is to long. Allowed length: %d / RealLength: %d', 4096, $realDataLen));
        }


        switch ($rawInputType) {
            case self::RAW_INPUT_TYPE_REQUEST:
                $packet = new RequestPacket($type, $id, $authenticator, $data);
                break;
            case self::RAW_INPUT_TYPE_RESPONSE:
                $packet = new ResponsePacket($type, $id, $authenticator, $data);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown input type "%s", allowed types: %s', $rawInputType, implode('|', [self::RAW_INPUT_TYPE_RESPONSE, self::RAW_INPUT_TYPE_REQUEST])));
        }

        while ($pos < $len) {
            $rawAttr = $this->rawAttributeHandler->parseRawAttribute($data, $pos);
            $pos += $rawAttr->getAttributeLength();
            if ($attribute = $this->attributeManager->deserializeRawAttribute($rawAttr, $packet)) {
                $packet->addAttribute($attribute);
            } else {
                $packet->addUnknownRawAttribute($rawAttr);
            }
        }
        return $packet;
    }

}
