<?php
require '../DomaintoolsAPI_class.inc.php';
// services => whois, whois/history, hosting-history, reverse-ip, name-server-domains, reverse-whois, domain-suggestions, domain-search, mark-alert, registrant-alert

$request = new DomaintoolsAPI();

$request->from('host-domains')
        ->withType('json')
        ->domain('66.249.17.251');

$response = $request->execute();

                                                 
echo $response;
