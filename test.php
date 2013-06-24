<?php

use Domaintools\Domaintools;

include('vendor/autoload.php');

$request = new \Domaintools\Domaintools();

  $request->from("whois")               // Name of the service
          ->withType("xml")             // Return type (JSON or XML or HTML)
          ->domain("domaintools.com");  // Domain name

  $response = $request->execute();
  //Display the response
  echo $response;
