<?php
require_once '../DomaintoolsAPI.class.php';
$request = new DomaintoolsAPI();
echo $request->from('whois')->domain('domaintools.com')->withType('xml')->execute();

