# SkyRadius

The first (known to me) RADIUS server which was implemented natively in PHP! Based on the incredible
possibilities of ReachtPHP I was now able to write this library. Currently only RFC2865 is implemented,
should follow RFC2866 + RFC2867 + RFC2868 (I'm always happy about PRs ;) ).

## Example

```PHP
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
$loader->load('./dictionary/ruckus.dictionary');

// add VendorSpecificAttribute
$radius->setVsaHandler(529, new IPv4AttributeHandler(), 139, 'Ascend-VSA-User-Acct-Host');

$packetCounter = $lastCount = 0;

$radius->on(SkyRadius::EVENT_PACKET, function (Context $context) use (&$packetCounter) {

    $response = $context->getResponse();
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
```

## Benchmark

So far I could not do any real tests, but first tests have shown that it is possible to more than 500 requests/sec.
I couldn't create more requests because my CPU was down. The SkyRadius-Server ran with 15% CPU and 10MB RAM. Here I 
would be pleased about experience values of users.

## TODOs

- Attribute Dictionary Loader
  - YAML
  - JSON
- UnitTest