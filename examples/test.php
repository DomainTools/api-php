<?php
require_once '../DomaintoolsAPI.class.php';
// services => whois, whois/history, hosting-history, reverse-ip, name-server-domains, reverse-whois, domain-suggestions, domain-search, mark-alert, registrant-alert

$request = new DomaintoolsAPI();

$request->from('domain-profile')
        ->withType('json')
        ->domain('domaintools.com');

//$response = $request->execute();

$response = new DomaintoolsAPIResponse($request);

echo $response->toJson();
