<?php
require_once '../DomaintoolsAPI.class.php';
$request = new DomaintoolsAPI();

// In you api.ini file, just uncomment this line
// host         = 'freeapi.domaintools.com'
// to use the freeapi on all you request


// You can also force a specific request to the freeapi by using the asFree() method
echo $request->from('whois')->domain('domaintools.com')->withType('json')->asFree()->execute();

// Note: you can for to the standard api by using asFree(false)
