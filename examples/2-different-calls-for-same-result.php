<?php
require_once '../DomaintoolsAPI.class.php';

/**
 * EXAMPLE - 3 different calls bringing the same result
 * service: whois
 * type   : json
 * domain : domaintools.com
 */

//call 1 (not recommened version)
$request1 = new DomaintoolsAPI();
$request1->domain('domaintools.com');
$request1->from('whois');
$request1->withType('json');
echo $request1->execute() . "\n";

// call 2 (recommended version if you only need the raw response)
$request2 = new DomaintoolsAPI();
echo $request2->from('whois')->domain('domaintools.com')->withType('json')->execute() . "\n";

// call 3 (recommended version if you perfer working with an object)
$request3 = new DomaintoolsAPI();
$response = $request3->from('whois')->domain('domaintools.com')->execute();
echo $response->toJson() . "\n";

