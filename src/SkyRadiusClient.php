<?php
declare(strict_types=1);

namespace SkyDiablo\SkyRadius;

use React\Datagram\SocketInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use SkyDiablo\SkyRadius\Exception\InvalidResponseException;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\Packet\RequestPacket;
use SkyDiablo\SkyRadius\Packet\ResponsePacket;
use SkyDiablo\SkyTtlList\SkyTtlList;

class SkyRadiusClient extends SkyRadius
{
    /**
     * @var SocketInterface
     */
    protected SocketInterface $socket;

    /**
     * @var int
     */
    private int $identifier = 0;

    /**
     * @var SkyTtlList
     */
    protected SkyTtlList $requestStack;

    /**
     * SkyRadiusClient constructor.
     * @param LoopInterface $loop
     * @param string $uri
     * @param string $psk
     * @param float $responseTimeout
     * @param AttributeManager|null $attributeManager
     */
    public function __construct(LoopInterface $loop, string $uri, string $psk, float $responseTimeout = 10.0, AttributeManager $attributeManager = null)
    {
        parent::__construct($loop, $uri, $psk, $attributeManager);
        $this->requestStack = new SkyTtlList($loop, $responseTimeout);
        $factory = new \React\Datagram\Factory($loop);

        $factory->createClient($uri)->then(function (\React\Datagram\Socket $client) {
            $this->socket = $client;
            $client->on('message', [$this, 'onMessage']);
            $client->on('error', function ($e) {
                $this->emit(self::EVENT_ERROR, [$e]);
            });
        });
    }

    /**
     * @param string $message
     * @param string $serverAddress
     * @param string $client
     * @throws Exception\SilentDiscardException
     */
    protected function onMessage(string $message, string $serverAddress, \React\Datagram\Socket $client)
    {
        /** @var ResponsePacket $responsePacket */
        $responsePacket = $this->handleRawInput($message, self::RAW_INPUT_TYPE_RESPONSE);
        $identifier = $responsePacket->getIdentifier();
        if (isset($this->requestStack[$identifier])) {
            /** @var RequestPacket $requestPacket */
            /** @var Deferred $def */
            [$requestPacket, $def] = $this->requestStack[$identifier] ?? [];
            if ($requestPacket && $def) {
                unset($this->requestStack[$identifier]); // clear request stack
                try {
                    $this->validateResponsePackage($requestPacket, $responsePacket);
                    $def->resolve($responsePacket);
                    $this->emit(self::EVENT_PACKET, [$requestPacket, $responsePacket, $serverAddress, $client]);
                } catch (InvalidResponseException $e) {
                    $def->reject($e);
                    $this->emit(self::EVENT_ERROR, [$e, $requestPacket, $responsePacket]);
                }
            }
        }
    }

    /**
     * @param RequestPacket $requestPacket
     * @param ResponsePacket $responsePacket
     * @throws InvalidResponseException
     */
    protected function validateResponsePackage(RequestPacket $requestPacket, ResponsePacket $responsePacket): void
    {
        $haystack = substr_replace($responsePacket->getRaw(), $requestPacket->getAuthenticator(), 4, SkyRadius::AUTHENTICATOR_LENGTH); //replace authenticator
        $md5 = md5($haystack . $this->psk, true);
        if (!($md5 === $responsePacket->getAuthenticator())) {
            throw new InvalidResponseException(sprintf('Authenticator mismatch! Response: %s, Calculated: %s', bin2hex($responsePacket->getAuthenticator()), bin2hex($md5)));
        }
    }

    /**
     * @param RequestPacket $requestPacket
     * @return string
     */
    protected function serializeRequestPacket(RequestPacket $requestPacket): string
    {
        $raw = $requestPacket->getRaw();
        return $this->packInt8($requestPacket->getType()) .
            $this->packInt8($requestPacket->getIdentifier()) .
            $this->packInt16(strlen($raw) + 20) . // 20 = type (1) + identifier (1) + length (2) + authenticator (16)
            $requestPacket->getAuthenticator() .
            $raw;
    }

    /**
     * @param int $type
     * @return RequestPacket
     * @throws \Exception
     */
    protected function createRequestPacket(int $type): RequestPacket
    {
        /*
        The Request Authenticator field in Accounting-Request packets contains a
        one-way MD5 hash calculated over a stream of octets consisting of the
        Code + Identifier + Length + 16 zero octets + request attributes + shared secret
        (where + indicates concatenation).
        */
        $authenticator = $type === PacketInterface::ACCOUNTING_REQUEST ? str_repeat(chr(0x00), self::AUTHENTICATOR_LENGTH) : random_bytes(self::AUTHENTICATOR_LENGTH);
        return new RequestPacket($type, ++$this->identifier, $authenticator, '');
    }

    /**
     * @param array $attributes
     * @param int $type e.g.: PacketInterface::ACCESS_REQUEST
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     * @throws \Exception
     */
    public function send(array $attributes, int $type = PacketInterface::ACCESS_REQUEST)
    {
        $def = new Deferred();
        $requestPacket = $this->createRequestPacket($type);
        $this->requestStack[$requestPacket->getIdentifier()] = [$requestPacket, $def];
        $attributesData = '';
        foreach ($attributes as $attribute) {
            $attributesData .= $this->attributeManager->serializeAttribute($attribute, $requestPacket);
        }
        $requestPacket->setRaw($attributesData);
        $this->socket->send($this->serializeRequestPacket($requestPacket));

        return $def->promise();
    }

}