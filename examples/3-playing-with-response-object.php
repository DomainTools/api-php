<?php
require_once '../DomaintoolsAPI.class.php';

/**
 * EXAMPLE - Playing with object response
 * service: whois
 * type   : json
 * domain : domaintools.com
 */

$request  = new DomaintoolsAPI();

// to get an object response => do not use withType(), toJson(), toXml(), toHtml() methods
$response = $request->domain('domaintools.com')->from('whois')->execute();

echo $response->registrant   ."\n";
echo $response->registration->created   ."\n";
echo $response->name_servers[0]   ."\n";
echo $response->whois->date . "\n";
echo $response->whois->record . "\n";

