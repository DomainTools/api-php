<?php
require_once '../DomaintoolsAPI.class.php';
// services => whois, whois/history, hosting-history, reverse-ip, name-server-domains, reverse-whois, domain-suggestions, domain-search, mark-alert, registrant-alert

$request = new DomaintoolsAPI();

$response = $request->from('mark-alert')
                    ->query('domaintools')
                    ->execute();

var_dump($response->toJson(true));
//$response->getRequest()->from('domain')

//echo $request->execute();

//$response = new DomaintoolsAPIResponse($request);
//echo $response->toJson();
