<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius;

use React\Datagram\Factory;
use React\Datagram\Socket;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use SkyDiablo\SkyRadius\Connection\Context;
use SkyDiablo\SkyRadius\Exception\InvalidRequestException;
use SkyDiablo\SkyRadius\Exception\InvalidServerResponseException;
use SkyDiablo\SkyRadius\Exception\SilentDiscardException;
use SkyDiablo\SkyRadius\Exception\SkyRadiusException;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\Packet\RequestPacket;
use SkyDiablo\SkyRadius\Packet\ResponsePacket;

use function React\Async\await;

class SkyRadiusServer extends SkyRadius
{
    public function __construct(string $uri, string $psk, AttributeManager $attributeManager = null, LoopInterface $loop = null)
    {
        parent::__construct($uri, $psk, $attributeManager, $loop);
        (new Factory($loop))
            ->createServer($uri)
            ->then(function (Socket $socket) {
                $socket->on('message', function (string $raw, string $peer, Socket $server) {
                    try {
                        /** @var RequestPacket $requestPacket */
                        $requestPacket = $this->handleRawInput($raw, self::RAW_INPUT_TYPE_REQUEST);
                        $this->validateRequestPackage($requestPacket);
                        $context = new Context($requestPacket);
                        $this->emit(self::EVENT_PACKET, [$context]);
                        await($this->sendResponse($context, $peer, $server));
                    } catch (SilentDiscardException $e) {
                        // silently ignored...
                        $this->emit(self::EVENT_PACKET_DISCARDED, [$e]);
                    } catch (SkyRadiusException $e) {
                        $this->emit(self::EVENT_ERROR, [$e]);
                    }
                });

                return $socket;
            })
            ->catch(function (\Throwable $e) {
                $this->emit(self::EVENT_ERROR, [new SkyRadiusException($e->getMessage(), $e->getCode(), $e)]);
            });
    }

    /**
     * @param Context $context
     * @param string  $peer
     * @param Socket  $socket
     *
     * @return PromiseInterface
     * @throws InvalidServerResponseException
     */
    protected function sendResponse(Context $context, string $peer, Socket $socket): PromiseInterface
    {
        $def = new Deferred();
        $def->promise()->then(function (ResponsePacket $responsePacket) use ($context, $peer, $socket, $def) {
            if (!$this->validateResponse($context)) {
                $def->reject(InvalidServerResponseException::create(context: $context));
            }
            try {
                $socket->send(
                    $this->serializeResponse(
                        $context->getResponse(),
                        $context->getRequest(),
                    ),
                    $peer,
                );
            } catch (\Throwable $e) {
                $def->reject($e);
            }

            return true;
        });
        $def->resolve($context->getResponse());

        return $def->promise();
    }

    /**
     * @param Context $context
     *
     * @return bool
     */
    protected function validateResponse(Context $context): bool
    {
        switch ($context->getRequest()->getType()) {
            case PacketInterface::ACCESS_REQUEST:
                return in_array($context->getResponse()->getType(), [
                    //an ACCESS-REQUEST must have an ACCESS-RESPONSE
                    PacketInterface::ACCESS_ACCEPT,
                    PacketInterface::ACCESS_REJECT,
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
     *
     * @return RequestPacket
     * @throws SilentDiscardException
     * @throws InvalidRequestException
     * @todo: you can change this value by "$this->socket->bufferSize". if this value is to small, this function have to
     * @todo: handle multiple input raw-input-data for the same request!
     * @todo: the input buffer is set to 65536 bytes (64MB) by default, this should be enough for now.
     */


    /**
     * @param RequestPacket $requestPacket
     *
     * @return bool
     * @throws InvalidRequestException
     */
    protected function validateRequestPackage(RequestPacket $requestPacket): bool
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
                $md5 = md5($haystack.$this->psk, true);
                if (!($md5 === $requestPacket->getAuthenticator())) {
                    throw new InvalidRequestException(sprintf('Authenticator mismatch! Request: %s, Calculated: %s', bin2hex($requestPacket->getAuthenticator()), bin2hex($md5)), PacketInterface::ACCOUNTING_REQUEST);
                }
            default:
                return true;
        }
    }

    /**
     * @param ResponsePacket $responsePacket
     * @param RequestPacket  $requestPacket
     *
     * @return string
     * @see https://tools.ietf.org/html/rfc2865#section-3
     */
    protected function serializeResponse(ResponsePacket $responsePacket, RequestPacket $requestPacket): string
    {
        $attributesData = '';
        foreach ($responsePacket->getAttributes() as $attribute) {
            $attributesData .= $this->attributeManager->serializeAttribute($attribute, $requestPacket);
        }
        $header = $this->packInt8($responsePacket->getType());
        $header .= $this->packInt8($responsePacket->getIdentifier());
        // +1 type as byte       \
        // +1 identifier as byte  \  = 20 byte
        // +16 = response-auth    /
        // +2 = "length" itself  /
        $header .= $this->packInt16(strlen($attributesData) + 20);
        $haystack
            = $header. // type + id + length
            $requestPacket->getAuthenticator().
            $attributesData.
            $this->psk;
        $responseAuth = md5($haystack, true);


        return $header.$responseAuth.$attributesData;
    }

}