<?php
require_once '../DomaintoolsAPI.class.php';
$request = new DomaintoolsAPI();
echo $request->from('whois')->domain('sympass.com')->withType('xml')->execute();

