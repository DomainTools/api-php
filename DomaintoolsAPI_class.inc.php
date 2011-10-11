<?php

/*
* This file is part of the domaintoolsAPI_php_wrapper package.
*
* Copyright (C) 2011 by domaintools.com - EuroDNS S.A. 
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

require("DomaintoolsAPIConfiguration_class.php");
require("lib/REST_Service/curl_rest_service_class.inc.php");
require_once("exceptions/ServiceException.class.php");
require_once("exceptions/ServiceUnavailableException.class.php");
require_once("exceptions/NotFoundException.class.php");
require_once("exceptions/NotAuthorizedException.class.php");
require_once("exceptions/InternalServerErrorException.class.php");
require_once("exceptions/BadRequestException.class.php");

/*
 * This class allow to call any service of the domaintoolsAPI
 * Example of call with default values :
	$response = domaintoolsAPI::from("info")->
							withType("json")->
							get("example.com");
							 
							 
 * Example of call with somes settings changes on the fly :
	$configuration = new domaintoolsAPIConfiguration();
	$configuration->set('username','anotherUsername')->
					set('password','anotherPassword');
	$response = domaintoolsAPI::from("info",$configuration)->
							withType("json")->
							get("example.com");
 */
class domaintoolsAPI{

    /**
     * Configuration (credentials, host,...)
     */
    private $configuration;
    
	  /**
     * Name of the service to call
     */
    private $serviceName;

    /**
     * Type of the return
     */
    private $returnType;

    /**
     * Url of the ressource to call
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
    * Array of domains/ips that can be called with no authentication or ip addresses restrictions
    * (ONLY for development usage; to test the API)
    **/
    private static $authorizedDomainsForTest = array("domaintools.com", "dailychanges.com", "nameintel.com", "reversewhois.com", "66.249.17.251");
    
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
     * Construct of the class, init the service name, build the url and init options
     * @param $serviceName
     */
    public function __construct($configuration=false) {

  		$this->configuration  = (empty($configuration))? new domaintoolsAPIConfiguration() : $configuration;
      $this->serviceName    = self::$mapServices['domain-profile'];
      $this->url            = $this->configuration->get('baseUrl');
      $this->options         = array();
    }
	
   /**
    * Specified the name of the service to call.
    * @param $serviceName name of the service
    * @return this
    */
    public function from($serviceName = '') {
        $this->serviceName = self::$mapServices[$serviceName];
        return $this;
    }

    /**
     * This function allows you to specify the return type of the service
     * @param $returnType return type (json, xml, html)
     * @return this
     */
    public function withType($returnType) {
        $this->returnType = $returnType;
        return $this;
    }
    
    /**
     * Set the domain name to use for the API request
     * @param $domainName
     * @return this
     */
    public function domain($domainName = '') {
      $this->domainName = $domainName;
      return $this;
    }
    
    /**
     * Make the request on the service, and return the response
     * @return the response of the service
     */
    public function execute($debug = false) {
        $response = "";
        $this->options['format'] = $this->getReturnType();
        $this->addCredentialsOptions();
        $url = $this->buildUrl();
        if($debug) return $url;
        $response = $this->request($url);

        return $response;
    }
    
    public function debug() {
      return $this->execute(true);
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
        $uri                         = '/'.$this->configuration->get('subUrl').(!empty($this->domainName)?'/'.$this->domainName.'/':'/').$this->serviceName;
        $this->options['timestamp']  = $timestamp;
        $this->options['signature']  = hash_hmac('md5', $api_username . $timestamp . $uri, $api_key);
      }
    }
    /**
     * Depending on the service name, we built the good url to request   
     * @return It returns the url
     */
    public function buildUrl() {
      //allow access to multiple values for the same GET/POST parameter without the use of the brace ([]) notation
      $query_string = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', http_build_query($this->options));
      
      $url = $this->url.(!empty($this->domainName)?'/'.$this->domainName.'/':'/').$this->serviceName."?".$query_string;
      return $url;
    }
    
    /**
     * This function allows you to specify an array of options
     * @param $options an array of options
     * @return this
     */
    public function where($options) {
        $this->options = array_merge($options, $this->options);
        return $this;
    }

    /**
     * Make a curl request on the service, and return the response if the http status code is 200,
     * else return an exception.
     * @param $url url to call
     * @return response of the service
     */
    private function request($url) {
      $transport = $this->configuration->get('transport');
      
		  try{
			  $response = $transport->get($url);

		  }catch(Exception $e){
		    /*echo $e->getMessage();
		    die($e->getTraceAsString());*/
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
     * @return return type
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
     * Force The configuration to use a given transport
     * @param RestServiceInterface $transport
     */
    public function setTransport(RestServiceInterface $transport) {
      $this->configuration->set('transport',$transport);
    }
}
?>
