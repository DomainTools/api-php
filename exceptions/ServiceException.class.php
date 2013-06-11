<?php

/**
 * This file is part of the domaintoolsAPI_php_wrapper package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ServiceException extends Exception {

    const INVALID_CONFIG_PATH    = "Config file does not exist";

    const UNKNOWN_SERVICE_NAME   = "Unknown service name";
    const EMPTY_API_KEY          = "Empty API key";
    const EMPTY_API_USERNAME     = "Empty API username";
    const UNKNOWN_RETURN_TYPE    = "Unknown return type. (json, xml or html required)";

    const INVALID_DOMAIN         = "Domain/Ip invalid";
    const INVALID_OPTIONS        = "Invalid options; options must be an array";

    const TRANSPORT_NOT_FOUND    = "Transport not found; it must refer to a class that implements RESTServiceInterface";
    const DOMAIN_CALL_REQUIRED   = "Domain is required for this service";
    const IP_CALL_REQUIRED       = "Ip address is required for this service";
    const EMPTY_CALL_REQUIRED    = "No domain or ip is required for this service";

    const INVALID_REQUEST_OBJECT = "Invalid object; DomaintoolsAPI instance required";
    const INVALID_JSON_STRING    = "Invalid json string; a valid one is required";

    public function __construct($message='', $code=0) {
        parent::__construct($message, $code);
    }
}
