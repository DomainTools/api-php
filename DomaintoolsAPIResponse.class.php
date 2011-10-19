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
  
  /**
   * Json string representing the response
   */
  private $json;
  
  /**
   * Json object representation
   */
  private $jsonObject;
  
  /**
   * Constructs the DomaintoolsAPIResponse object
   * @param DomaintoolsAPI $request the request object
   * @param string $json json string from request
   */
  public function __construct($request = null, $json = null) {
    
    if(!$request instanceof DomaintoolsAPI) {
      throw new ServiceException(ServiceException::INVALID_REQUEST_OBJECT);
    }
    
    $this->mergeJson($json);
        
    $this->request = $request;
  }
  
  /**
   * Magic get method to create an alias :
   * $this->history <=> $this->jsonObject->response->history
   * if      $this->history already exists                => return it
   * elseif  $this->jsonObject->response->history exists  => return it
   * else                                                 => return null
   */
  public function __get($name) {
    if(isset($this->$name)) {
      return $this->$name;
    } 
    elseif($this->jsonObject->response->$name) {
      return $this->jsonObject->response->$name;
    } 
    return null;
    
  }
  
  /**
   * declare $this->json and $this->jsonObject only if json_decode worked
   * otherwise we keep the old values
   * @param string $json
   */
  public function mergeJson($json) {
  
    $object = json_decode($json);
    
    if($object === null) {
      throw new ServiceException(ServiceException::INVALID_JSON_STRING);
    }
    
    $this->json       = $json;
    $this->jsonObject = $object;  
  }
  
  /**
   * Force "json" as render type and execute the request
   * @param boolean $refresh (if true we force request + merge with DomaintoolsAPIResponse)
   * @return string $this->json
   */
  public function toJson($refresh = false) {
  
    if($refresh) { 
      $json = $this->request->withType('json')->execute();
      $this->mergeJson($json);
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
