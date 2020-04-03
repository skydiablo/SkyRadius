#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App;

use SkyDiablo\SkyRadius\Dictionary\FreeRadiusDictionaryLoader;
use SkyDiablo\SkyRadius\Attribute\AttributeInterface;
use SkyDiablo\SkyRadius\Attribute\StringAttribute;
use SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler;
use SkyDiablo\SkyRadius\Connection\Context;
use SkyDiablo\SkyRadius\Packet\PacketInterface;
use SkyDiablo\SkyRadius\SkyRadius;

require __DIR__ . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$radius = new SkyRadius($loop, '0.0.0.0:3500', 'test');

// load freeRADIUS dictionary files
$loader = new FreeRadiusDictionaryLoader($radius);
$loader->load('./dictionary/hostapd.dictionary');

// add VendorSpecificAttribute
$radius->setVsaHandler(529, new IPv4AttributeHandler(), 139, 'Ascend-VSA-User-Acct-Host');

$packetCounter = $lastCount = 0;

$radius->on(SkyRadius::EVENT_PACKET, function (Context $context) use (&$packetCounter) {

    $response = $context->getResponse();

    // select request attributes
    $attrs = $context->getRequest()->getAttribute(
            AttributeInterface::ATTR_CALLING_STATION_ID, // get by attribute id (int)
            AttributeInterface::ATTR_TYPE_ALIAS[AttributeInterface::ATTR_NAS_PORT] // get by alias (string)
    );

    $response->addAttributes([
        // add new Response Attribute
        new StringAttribute(AttributeInterface::ATTR_REPLY_MESSAGE, 'Echo Test-Radius-Server'),
    ]);

    // echo all incoming attributes, for testing only
    $response->addAttributes($context->getRequest()->getAttributes());

    // accept request, by default all requests will be rejected
    $response->setType(PacketInterface::ACCESS_ACCEPT);

    $packetCounter++; // for benchmarking
});


$loop->addPeriodicTimer(1, function () use (&$lastCount, &$packetCounter) {
    $intervalCount = $packetCounter - $lastCount;
    $lastCount = $packetCounter;
    $message = sprintf("PacketCount: %d / IntervalCount: %d\n", $packetCounter, $intervalCount);

    // reactPHP community, please dont catch me, but a STDOUT stream handler seems not working in windows environments?
    echo $message;
});

$loop->run();