<?php
require_once '../DomaintoolsAPI.class.php';

/**
 * EXAMPLE - Playing with object response
 * service: domain-profile
 * type   : json
 * domain : domaintools.com
 */

$request  = new DomaintoolsAPI();


// from() method is not precise, so the default service is domain-profile
// to get an object response => do not chain withType() method
$response = $request->domain('domaintools.com')->execute();

echo $response->toJson()   ."\n";
echo $response->toXml()    ."\n";
echo $response->toHtml()   ."\n";
var_dump($response->toArray());
var_dump($response->toObject());

