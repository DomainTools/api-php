<?php
/**
 * This file is part of the domaintoolsAPI_php_wrapper package.
 *
 * Copyright (C) 2011 by domaintools.com - EuroDNS S.A. 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
 /**  
  @example of call returning a DomaintoolsResponse Object :

  // configure the request
  $request      = new DomaintoolsAPI();
  $request->from('whois')
          ->withType('json')
          ->domain('domaintools.com');

  // send request to the response object
  $response     = new DomaintoolsAPIResponse($request);
  
  // return responses
  $jsonResponse = $response->toJson();
  $xmlResponse  = $response->toXml();  
  
 */
  
class DomaintoolsAPIResponse {

  /**
   * Request object for API
   */
  private $api;
  
  /**
   * Constructs the DomaintoolsAPIResponse object
   * @param DomaintoolsAPI $api the request object
   */
  public function __construct(DomaintoolsAPI $api) {
    $this->api = $api;
  }
  
  /**
   * Force "json" as render type and execute the request 
   * @return string Json
   */
  public function toJson() {
    return $this->api->withType('json')->execute();
  }
  
  /**
   * Force "json" as render type and execute the request 
   * Converts the Json to an stdClass object
   * @return stdClass
   */  
  public function toObject() {
    return json_decode($this->toJson());
  }
  
  /**
   * Force "json" as render type and execute the request 
   * Converts the Json to an array
   * @return array
   */   
  public function toArray() {
    return json_decode($this->toJson(), true);
  }
  
  /**
   * Force "xml" as render type and execute the request 
   * @return string xml
   */  
  public function toXml() {
    return $this->api->withType('xml')->execute();
  }
  
  /**
   * Force "html" as render type and execute the request 
   * @return string html
   */ 
  public function toHtml() {
    return $this->api->withType('html')->execute();
  }
}
