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
  
  const UNKNOWN_SERVICE_NAME   = "Unknown service name";
  const EMPTY_API_KEY          = "Empty API key";
  const EMPTY_API_USERNAME     = "Empty API username";
  const UNKNOWN_RETURN_TYPE    = "Unknown return type. (json or xml or html required)";
  
  const INVALID_DOMAIN         = "Domain/Ip invalide";
}
?>
