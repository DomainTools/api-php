<?php

/**
 * This file is part of the domaintoolsAPI_php_wrapper package.
 *
 * Copyright (C) 2011 by domaintools.com - EuroDNS S.A. 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require("DomaintoolsAPIConfiguration.class.php");
require_once("DomaintoolsAPIResponse.class.php");

require_once("lib/REST_Service/curl_rest_service_class.inc.php");
require_once("exceptions/ServiceException.class.php");
require_once("exceptions/ServiceUnavailableException.class.php");
require_once("exceptions/NotFoundException.class.php");
require_once("exceptions/NotAuthorizedException.class.php");
require_once("exceptions/InternalServerErrorException.class.php");
require_once("exceptions/BadRequestException.class.php");

/**
  This class allow to call any service of the domaintoolsAPI
  @example of call with default values :
	$request = new DomaintoolsAPI();
  $request->from("whois")
	        ->withType("xml")
		      ->domain("example.com");
		      
  $xmlResponse = $request->execute();
							 
							 
  @example of call with somes settings changes on the fly :
	$configuration = new domaintoolsAPIConfiguration();
	$configuration->set('username','anotherUsername')
					      ->set('password','anotherPassword');
					      
	$request = new DomaintoolsAPI($configuration);
  $request->from("whois")
          ->withType("json")
					->domain("example.com");
					
  $jsonResponse = $request->execute():
  
  @example of call returning a DomaintoolsResponse Object :

  $request = new DomaintoolsAPI();
  $request->from('whois')
          ->domain('domaintools.com');
          
  $response = $request->execute();
  
  $jsonResponse = $response->toJson();
  $xmlResponse = $response->toXml();  
  
 */
class domaintoolsAPI {

    /**
     * Configuration (credentials, host,...)
     */
    private $configuration;
    
	  /**
     * Name of the service to call
     */
    private $serviceName;

	  /**
     * Default Name of the service (if none given)
     */
    private $defaultServiceName = 'domain-profile';
        
	  /**
     * Representation of the service in the url to call
     * In most of cases, serviceName = serviceUri
     * @example for serviceName = "domain-profile", serviceUri = ""
     * @example for serviceName = "whois-lookup",   serviceUri = "whois"
     */
    private $serviceUri;    

    /**
     * Map table which associate serviceName to serviceUri
     */
    private static $mapServices = array(
      ''                   => '',
      'domain-profile'     => '',
      'whois-lookup'       => 'whois',
      'whois'              => 'whois',
      'whois-history'      => 'whois/history',
      'whois/history'      => 'whois/history',
      'hosting-history'    => 'hosting-history',
      'reverse-ip'         => 'reverse-ip',
      'host-domains'       => 'host-domains',
      'name-server-domains'=> 'name-server-domains',
      'name-server-report' => 'name-server-domains',
      'reverse-whois'      => 'reverse-whois',
      'domain-suggestions' => 'domain-suggestions',
      'domain-search'      => 'domain-search',
      'mark-alert'         => 'mark-alert',
      'brand-alert'        => 'mark-alert',
      'registrant-alert'   => 'registrant-alert'
    );
    
    /**
     * regex to validate an ip
     */
    const IP_REGEX         = "#^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$#";
    
    /**
     * regex to validate a hostname
     */
    const HOSTNAME_REGEX   = "#^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$#";
    
    /**
     * A call with a domain
     */
    const DOMAIN_CALL      = 1;
    /**
     * A call with an ip address
     */    
    const IP_CALL          = 2;
    /**
     * A call with no domain and no ip address
     */    
    const EMPTY_CALL       = 0;
    /**
     * For a given service (key), we have call ($value)
     */
    private static $mapServicesCalls = array(
      ''                   => self::DOMAIN_CALL,
      'whois'              => self::DOMAIN_CALL,
      'whois/history'      => self::DOMAIN_CALL,
      'hosting-history'    => self::DOMAIN_CALL,
      'reverse-ip'         => self::DOMAIN_CALL,
      'host-domains'       => self::IP_CALL,
      'name-server-domains'=> self::EMPTY_CALL,
      'reverse-whois'      => self::EMPTY_CALL,
      'domain-suggestions' => self::EMPTY_CALL,
      'domain-search'      => self::EMPTY_CALL,
      'mark-alert'         => self::EMPTY_CALL,
      'brand-alert'        => self::EMPTY_CALL,
      'registrant-alert'   => self::EMPTY_CALL
    );

    /**
     * Type of the return
     */
    private $returnType = null;
    
    /**
     * Authorized return types
     */    
    private static $authorizedReturnTypes = array('json', 'xml', 'html');
    
    /**
     * Url of the resource to call
     */
    private $url;

    /**
     * An array of options
     */
    private $options;

    /**
     * Name of the domain to use
     */
    private $domainName;

    /**
     * rawResponse sent by domaintoolsAPI
     */    
    private $rawResponse;
    
    /**
    * Array of domains/ips that can be called with no authentication or ip addresses restrictions
    * (ONLY for development usage; to test the API)
    **/
    private static $authorizedDomainsForTest = array("domaintools.com", "dailychanges.com", "nameintel.com", "reversewhois.com", "66.249.17.251");
    
    /**
     * Construct of the class with an optional given configuration object
     * @param DomaintoolsConfigurationAPI $configuration
     */
    public function __construct($configuration=false) {

  		$this->configuration  = (empty($configuration))? new domaintoolsAPIConfiguration() : $configuration;
  		
      $this->serviceName    = $this->defaultServiceName;
      $this->serviceUri     = self::$mapServices[$this->defaultServiceName];
      $this->options        = array();
    }
	
   /**
    * Specifies the name of the service to call.
    * @param string $serviceName name of the service
    * @return DomaintoolsAPI $this
    */
    public function from($serviceName = '') {
        if(!array_key_exists($serviceName, self::$mapServices)) {
          throw new ServiceException(ServiceException::UNKNOWN_SERVICE_NAME);
        }
        $this->serviceName = $serviceName;
        $this->serviceUri  = self::$mapServices[$serviceName];        
        return $this;
    }

    /**
     * This function allows you to specify the return type of the service
     * @param string $returnType (json, xml, html)
     * @return DomaintoolsAPI $this
     */
    public function withType($returnType) {
      if(!in_array($returnType, self::$authorizedReturnTypes)) {
        throw new ServiceException(ServiceException::UNKNOWN_RETURN_TYPE);
      }
      $this->returnType = $returnType;
      return $this;
    }
    
    /**
     * Set the domain name to use for the API request
     * @param string $domainName (has to be an IP address OR a domain)
     * @return DomaintoolsAPI $this
     * @todo check we have a valid domain
     */
    public function domain($domainName = '') {
      // domainName has to be a valid Domain or a valid IP
      
      /*if(!preg_match(self::HOSTNAME_REGEX, $domainName) && 
         !preg_match(self::IP_REGEX, $domainName)) {
         throw new ServiceException(ServiceException::INVALID_DOMAIN); 
      }*/
        
      $this->domainName = $domainName;
      return $this;
    }

    /**
     * Make the request on the service, and return the response
     * @return string $rawResponse (if a returnType is specified)
     * @return DomaintoolsAPIResponse $response (if no returnType is specified)
     */
    public function execute($debug = false) {
    
        $rawResponse = "";
        $this->buildOptions();
        $this->validateSettings();
        
        if(empty($this->returnType)) {
          $this->options['format'] = 'json';
        }
        
        $this->buildUrl();
        
        if($debug) {
          return $this->url;
        }
        
        $this->rawResponse = $this->request();
        
        if(empty($this->returnType)) {
          return new DomaintoolsAPIResponse($this, $this->rawResponse);
        }  

        return $this->rawResponse;
    }
    
    public function debug() {
      return $this->execute(true);
    }
    
    /**
     * Checks all the settings are good before calling the request
     * Checks if a service required a domain, an ip, or nothing
     */
    private function validateSettings() {
    
        $isIp     = preg_match(self::IP_REGEX, $this->domainName);
        $isDomain = preg_match(self::HOSTNAME_REGEX, $this->domainName);
        
        
        if(self::$mapServicesCalls[$this->serviceUri] == self::DOMAIN_CALL && !$isDomain) {
          throw new ServiceException(ServiceException::DOMAIN_CALL_REQUIRED);
        }
        
        if(self::$mapServicesCalls[$this->serviceUri] == self::IP_CALL && !$isIp) {
          throw new ServiceException(ServiceException::IP_CALL_REQUIRED);
        }
        
        if(self::$mapServicesCalls[$this->serviceUri] == self::EMPTY_CALL && !empty($this->domainName)) {
          throw new ServiceException(ServiceException::EMPTY_CALL_REQUIRED);
        }
    }
    
    /**
     * Add credentials to the Options array (if necessary).
     * First, we check if the domain name (or ip address) is authorized for a free testing of the API
     * If so, no credentials options will be added
     * If not, credentials are added
     */
    public function addCredentialsOptions() {
    
      //if(in_array($domainName, self::$authorizedDomainsForTest)) return;
      
      $api_username = $this->configuration->get('username');
      $api_key      = $this->configuration->get('password');
      
      $this->options['api_username'] = $api_username;
      $this->options['api_key']      = $api_key;
      
      if($this->configuration->get('secureAuth')) {
        $timestamp                   = gmdate("Y-m-d\TH:i:s\Z");
        $uri                         = '/'.$this->configuration->get('subUrl').(!empty($this->domainName)?'/'.$this->domainName.'/':'/').$this->serviceUri;
        $this->options['timestamp']  = $timestamp;
        $this->options['signature']  = hash_hmac('md5', $api_username . $timestamp . $uri, $api_key);
      }
    }
    
    public function buildOptions() {
      $this->options['format'] = $this->getReturnType();
      $this->addCredentialsOptions();
    }

    /**
     * Depending on the service name, and the options we built the good url to request   
     */
    public function buildUrl() {
      //allow access to multiple values for the same GET/POST parameter without the use of the brace ([]) notation
      $query_string = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', http_build_query($this->options));
      
      $this->url = $this->configuration->get('baseUrl').(!empty($this->domainName)?'/'.$this->domainName.'/':'/').$this->serviceUri."?".$query_string;
    }

    /**
     * This function allows you to specify an array of options
     * @param array $options an array of options
     * @return DomaintoolsAPI $this
     */
    public function where($options) {
        if(!is_array($options)) throw new ServiceException(ServiceException::INVALID_OPTIONS);
        $this->options = array_merge($options, $this->options);
        return $this;
    }
    
    public function query($query) {
      return $this->where(
        array('query' => $query)
      );
    }

    /**
     * Make a curl request on the service, and return the response if the http status code is 200,
     * else return an exception.
     * @return string|DomaintoolsAPI response of the service
     */
    private function request() {
      $transport = $this->configuration->get('transport');
      
		  try{
			  $response = $transport->get($this->url);

		  }catch(Exception $e){
			  throw new ServiceUnavailableException();
		  }

      $status = $transport->getStatus();

      if($status != null){
          switch($status){
              case 200:
                  return $response;
              case 400:
                  throw new BadRequestException();
              case 403:
                  throw new NotAuthorizedException();
              case 404:
                  throw new NotFoundException();
              case 500:
                  throw new InternalServerErrorException();
              case 503:
                  throw new ServiceUnavailableException();
              default:                	
                  throw new ServiceException();
          }
      }else{
          throw new ServiceException('Response is empty');
      }
    }

    /**
     * Getter of the return type
     * @return string $returnType
     */
    private function getReturnType() {
      if($this->returnType != null){
          $returnType = $this->returnType;
      }else{
          $returnType = $this->configuration->get('returnType');
      }
      return $returnType;
    }
    
    /**
     * Getter of the service name
     * @return string $this->serviceName
     */
    public function getServiceName() {
      
      return $this->serviceName;
    }

    /**
     * Getter of the service uri
     * @return string  $this->serviceUri
     */    
    public function getServiceUril() {
      
      return $this->serviceUri;
    }
    
    /**
     * Getter of the default service name
     * @return string $this->defaultServiceName
     */    
    public function getDefaultServiceName() {
      
      return $this->defaultServiceName;
    }
    
    /**
     * Getter of the options
     * @return array $this->options
     */    
    public function getOptions() {
      
      return $this->options;
    }       
    /**
     * Force The configuration to use a given transport
     * @param RestServiceInterface $transport
     */
    public function setTransport(RestServiceInterface $transport) {
      $this->configuration->set('transport',$transport);
    }
    
    /**
     * Force a value for the response sent by the API
     * @param mixed $response
     */
    public function setRawResponse($response) {
      $this->rawResponse = $response;
    }
}
?>
