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

    /*
     * Configuration (credentials, host,...)
     */
    private $configuration;
	 /*
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
    public function __construct($serviceName,$configuration=false){
		  if(!$configuration) $configuration = new domaintoolsAPIConfiguration();
  		$this->configuration = $configuration;
      $this->serviceName = self::$mapServices[$serviceName];
      $this->url = $this->configuration->get('baseUrl');
      $this->options = array();
    }
	
	/**
     * Specified the name of the service to call. Build an object domaintoolsAPI
     * @param $serviceName name of the service
	 * @param $configuration configuration object, if change needed from default settings
     * @return this
     */
    public static function from($serviceName = '',$configuration=false){
        $me = __CLASS__;
        return new $me($serviceName,$configuration);
    }

    /**
     * This function allows you to specify the return type of the service
     * @param $returnType return type (json, xml, html)
     * @return this
     */
    public function withType($returnType){
        $this->returnType = $returnType;
        return $this;
    }

    /**
     * Make the request on the service, and return the response
     * @param $domainName
     * @param $decode not totally implements
     * @return the response of the service
     */
    public function get($domainName = ''){
        $response = "";
        $this->options['format'] = $this->getReturnType();
        $this->addCredentialsOptions($domainName);
        $url = $this->buildUrl($domainName);
        //print_r($this->options);die;
        //die($url);
        $response = $this->request($url);

        return $response;
    }
    /**
     * Add credentials to the Options array (if necessary).
     * First, we check if the domain name (or ip address) is authorized for a free testing of the API
     * If so, no credentials options will be added
     * If not, credentials are added
     * @param $domainName
     */
    public function addCredentialsOptions($domainName = ''){
    
      if(in_array($domainName, self::$authorizedDomainsForTest)) return;
      
      $api_username = $this->configuration->get('username');
      $api_key      = $this->configuration->get('password');
      
      $this->options['api_username'] = $api_username;
      $this->options['api_key']      = $api_key;
      
      if($this->configuration->get('secureAuth')){
        $timestamp                   = gmdate("Y-m-d\TH:i:s\Z");
        $uri                         = '/'.$this->configuration->get('subUrl').(!empty($domainName)?'/'.$domainName.'/':'/').$this->serviceName;
        $this->options['timestamp']  = $timestamp;
        $this->options['signature']  = hash_hmac('md5', $api_username . $timestamp . $uri, $api_key);
      }
    }
    /**
     * Depending on the service name, we built the good url to request
     * @param $domainName     
     * @return It returns the url
     */
    public function buildUrl($domainName = ''){
      //allow access to multiple values for the same GET/POST parameter without the use of the brace ([]) notation
      $query_string = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', http_build_query($this->options));
      
      $url = $this->url.(!empty($domainName)?'/'.$domainName.'/':'/').$this->serviceName."?".$query_string;
      return $url;
    }
    
    /**
     * This function allows you to specify an array of options
     * @param $options an array of options
     * @return this
     */
    public function where($options){
        $this->options = array_merge($options, $this->options);
        return $this;
    }

    /**
     * Make a curl request on the service, and return the response if the http status code is 200,
     * else return an exception.
     * @param $url url to call
     * @return response of the service
     */
    private function request($url){
        $curlRestService = new CurlRestService();
        $content_Type = 'application/json';
        $curlRestService->setOption('CURLOPT_TIMEOUT', 10);
        $curlRestService->setOption('CURLOPT_CUSTOM_HTTPHEADER', 'Content-Type: '.$content_Type);
		try{
			$response = $curlRestService->get($url);	
		}catch(Exception $e){
			throw new ServiceUnavailableException();
		}
        $info = $curlRestService->getInfo();

        if($info['http_code'] != null){
            switch($info["http_code"]){
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
    private function getReturnType(){
        if($this->returnType != null){
            $returnType = $this->returnType;
        }else{
            $returnType = $this->configuration->get('returnType');
        }
        return $returnType;
    }
}
?>
