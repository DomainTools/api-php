<?php
require_once '../DomaintoolsAPI.class.php';

/**
 * EXAMPLE - 3 different calls bringing the same result
 * service: whois
 * type   : json
 * domain : domaintools.com
 */

//call 1 (not recommended version)
$request1 = new DomaintoolsAPI();
$request1->domain('domaintools.com');
$request1->from('whois');
$request1->withType('json');
echo $request1->execute() . "\n";

// call 2 (recommended version if you only need the raw response)
// toJson() is an alias of withType('json')
$request2 = new DomaintoolsAPI();
echo $request2->from('whois')->domain('domaintools.com')->toJson()->execute() . "\n";

// call 3 (alternative version if you are looking for a method to return the rawResponse)
$request3 = new DomaintoolsAPI();
$request3->from('whois')->domain('domaintools.com')->toJson()->execute();
echo $request3->getRawResponse() . "\n";

