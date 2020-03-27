# SkyRadius

The first (known to me) RADIUS server which was implemented natively in PHP! Based on the incredible
possibilities of ReachtPHP I was now able to write this library. Currently only RFC2865 is implemented,
should follow RFC2866 + RFC2867 + RFC2868 (I'm always happy about PRs ;) ).

## Example

```PHP
$loop = \React\EventLoop\Factory::create(); // create react-php event-loop
$radius = new \SkyDiablo\SkyRadius\SkyRadius($loop, '0.0.0.0:3500', 'test'); // create radius-server 

// add VendorSpecificAttribute
$radius->setVsaHandler(529, new \SkyDiablo\SkyRadius\AttributeHandler\IPv4AttributeHandler(), 139, 'Ascend-VSA-User-Acct-Host');

$packetCounter = $lastCount = 0;

$radius->on(SkyRadius::EVENT_PACKET, function (Context $context) use (&$packetCounter) {
    $response = $context->getResponse(); // pre defined response object
    $response->addAttributes([
        // add new Response Attribute
        new StringAttribute(AttributeInterface::ATTR_REPLY_MESSAGE, 'That\'s a great test.'),
    ]);

    // echo all incoming attributes, for testing only
    $response->addAttributes($context->getRequest()->getAttributes());

    // accept request, by default all requests will be rejected
    $response->setType(PacketInterface::ACCESS_ACCEPT);

    $packetCounter++; // for benchmarking
});

// echo every second the processed requests 
$loop->addPeriodicTimer(1, function () use (&$lastCount, &$packetCounter) {
    $intervalCount = $packetCounter - $lastCount;
    $lastCount = $packetCounter;
    $message = sprintf("PacketCount: %d / IntervalCount: %d\n", $packetCounter, $intervalCount);
    echo $message;
});

$loop->run(); // start the loop
```

## Benchmark

So far I could not do any real tests, but first tests have shown that it is possible to more than 500 requests/sec.
I couldn't create more requests because my CPU was down. The SkyRadius-Server ran with 15% CPU and 10MB RAM. Here I 
would be pleased about experience values of users.

## TODOs

- Attribute Dictionary Loader
  - YAML
  - JSON
  - freeRADIUS Dictionary
- UnitTest