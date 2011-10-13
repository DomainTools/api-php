<?php
require '../DomaintoolsAPI_class.inc.php';
// services => whois, whois/history, hosting-history, reverse-ip, name-server-domains, reverse-whois, domain-suggestions, domain-search, mark-alert, registrant-alert

$request = new DomaintoolsAPI();

$request->from('domain-profile')
        ->withType('json')
        ->domain('domaintools.com');

$response = $request->execute();
                                  
echo $response;
