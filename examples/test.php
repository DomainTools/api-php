<?php
require '../DomaintoolsAPI_class.inc.php';
// services => whois, whois/history, hosting-history, reverse-ip, name-server-domains, reverse-whois, domain-suggestions, domain-search, mark-alert, registrant-alert
$response = DomaintoolsAPI::from('domain-profile')
                          ->withType("xml")
                          ->get('domaintools.com');                         
echo $response;
