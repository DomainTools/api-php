<?php
require_once '../DomaintoolsAPI.class.php';

/**
 * EXAMPLE - 3 different calls bringing the same result
 * service: whois
 * type   : json
 * domain : domaintools.com
 */

//call 1
$request1 = new DomaintoolsAPI();
echo $request1->from('whois')->domain('domaintools.com')->withType('json')->execute() . "\n";

// call 2
$request2 = new DomaintoolsAPI();
echo $request2->from('whois')->domain('domaintools.com')->withType('json')->execute() . "\n";

// call 3
$request3 = new DomaintoolsAPI();
$response = $request3->from('whois')->domain('domaintools.com')->execute();
echo $response->toJson() . "\n";

