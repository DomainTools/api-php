<?php

/*
* This file is part of the domainAPI_php_wrapper package.
*
* Copyright (C) 2011 by domainAPI.com - EuroDNS S.A. 
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

class ServiceException extends Exception {
  
  const INVALID_CONFIG_PATH    = "Config file do not exist";
  
  const UNKNOWN_SERVICE_NAME   = "Unknown service name";
  const EMPTY_API_KEY          = "Empty API key";
  const EMPTY_API_USERNAME     = "Empty API username";
  const UNKNOWN_RETURN_TYPE    = "Unknown return type. (json or xml or html required)";
  
  const INVALID_DOMAIN         = "Domain/Ip invalide";
  const INVALID_OPTIONS        = "Invalid options; options must be an array";
  
  const TRANSPORT_NOT_FOUND    = "Transport not found; it must refer to a class that implements RESTServiceInterface";
  const DOMAIN_CALL_REQUIRED   = "Domain is required for this service";
  const IP_CALL_REQUIRED       = "Ip address is required for this service";
  const EMPTY_CALL_REQUIRED    = "No domain or ip is required for this service"; 
  
  const INVALID_REQUEST_OBJECT = "Invalid object; DomaintoolsAPI instance required";   
}
?>
