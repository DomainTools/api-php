<?php
require_once '../DomaintoolsAPI.class.php';
// services => whois, whois/history, hosting-history, reverse-ip, name-server-domains, reverse-whois, domain-suggestions, domain-search, mark-alert, registrant-alert


$request = new DomaintoolsAPI();

$response = $request->domain('domaintools.com')
                    ->from('whois')
                    ->execute();

var_dump($response->toJson());

