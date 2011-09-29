<?php
require '../DomaintoolsAPI_class.inc.php';
// services => whois, whois/history, hosting-history, reverse-ip, name-server-domains, reverse-whois, domain-suggestions, domain-search, mark-alert, registrant-alert
$response = DomaintoolsAPI::from('registrant-alert')
                          ->withType("xml")
                          ->where(array('query' => 'domaintools'))
                          ->get();                         
echo $response;
