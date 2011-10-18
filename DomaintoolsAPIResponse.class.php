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
  $response     = $request->from('whois')
                          ->domain('domaintools.com')
                          ->execute();

  // send request to the response object
  $response     = new DomaintoolsAPIResponse($request);
  
  // return responses
  echo $response->whois->date;
  echo $response->toXml();  
  
 */
  
class DomaintoolsAPIResponse {

  /**
   * Request object for API
   */
  private $request;
  private $json;
  
  /**
   * Constructs the DomaintoolsAPIResponse object
   * @param DomaintoolsAPI $request the request object
   * @param string $json json string from request
   */
  public function __construct($request, $json) {
    if(!$request instanceof DomaintoolsAPI) {
      throw new ServiceException(ServiceException::INVALID_REQUEST_OBJECT);
    }
    
    $this->request = $request;
    $this->json    = $json;
    
    $this->mergeJson(json_decode($json));
  }
  
  /**
   * Merge stdClass Json object with DomaintoolsAPIResponse
   * @param stdClass $obj (object representation of json)
   */
  public function mergeJson(&$obj) {
    foreach($obj as $key => $value) {
      
      if($key != 'response') {
        $this->$key = $value;
      }
      
      if($value instanceof stdClass) {
        $this->mergeJson($value);
      } 
    }
  }
  
  /**
   * Force "json" as render type and execute the request
   * @param boolean $refresh (if true we force request + merge with DomaintoolsAPIResponse)
   * @return string $this->json
   */
  public function toJson($refresh = false) {
  
    if($refresh) { 
      $json = $this->request->withType('json')->execute();
      $this->mergeJson(json_decode($json));
      $this->json = $json;
    }
    return $this->json;
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
    return $this->request->withType('xml')->execute();
  }
  
  /**
   * Force "html" as render type and execute the request 
   * @return string html
   */ 
  public function toHtml() {
    return $this->request->withType('html')->execute();
  }
  /**
   * Getter of the request object (DomaintoolsAPI)
   */
  public function getRequest() {
    return $this->request;
  }
}
