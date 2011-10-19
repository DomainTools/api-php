<?php
/*
* This file is part of the domaintoolsAPI_php_wrapper package.
*
* Copyright (C) 2011 by domaintools.com - EuroDNS S.A. 
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

require_once("lib/REST_Service/curl_rest_service_class.inc.php");
require_once("exceptions/ServiceException.class.php");

class DomaintoolsAPIConfiguration {

	/**
	 * Server Host
	 */
	private $host;
	
	/**
	 * Server Port
	 */
	private $port;
	
	/**
	 * Sub URL (version)
	 */
	private $subUrl;
	
	/**
	 * Complete URL
	 */
	private $baseUrl;
	
	/**
	 * Domaintools API Username
	 */
	private $username;
	
	/**
	 * Domaintools API Password
	 */
	private $password;
	
	/**
	 * (Boolean) force to secure authentication
	 */
	private $secureAuth;
	
	/**
	 * Default return type (json/xml/html)
	 */
	private $returnType;
	
	/**
	 * transport type (curl, etc.)
	 */
	private $transportType;
	
	/**
	 * Object in charge of calling the API
	 */
	private $transport;
	
	/**
	 * default config file path
	 */
	private $defaultConfigPath;
	
	/**
	 * default configuration 
	 * (that will be use to complete if necessary)
	 */
	private $defaultConfig = array(
      'username'       => '',
      'key'            => '',
      'host'           => 'api.domaintools.com',
      'port'           => '80',
      'version'        => 'v1',
      'secure_auth'    => true,
      'return_type'    => 'json',
      'transport_type' => 'curl',
      'content_type'   => 'application/json'
   );
   
	/**
   * Construct of the class and initiliaze with default values (if no config given)
   * @param mixed $ini_resource
   */
	public function __construct($ini_resource = '') {
	  
	  $this->defaultConfigPath = __DIR__.'/api.ini';
	  
	  if(empty($ini_resource)) $ini_resource = $this->defaultConfigPath;

	  if(!is_array($ini_resource)) {
	    if(!file_exists($ini_resource)) { 
	      throw new ServiceException(ServiceException::INVALID_CONFIG_PATH);
	    }
      $config = parse_ini_file($ini_resource);
    }
    elseif(is_array($ini_resource)) { 
	    $config = $ini_resource;
	  }
	  $this->init($config);
  }   
  
  /**
   * Initialize the configuration Object
   * @param array $config - Associative array for configuration
   */
  private function init($config = array()) {
  
 	  $config                         = $this->validateParams($config);
	  
		$this->host 					          = $config['host'];
		$this->port						          = $config['port'];
		$this->subUrl					          = $config['version'];
		$this->username					        = $config['username'];
		$this->password					        = $config['key'];
		$this->secureAuth               = $config['secure_auth'];
		$this->returnType				        = $config['return_type'];
		$this->contentType				      = $config['content_type'];
		$this->transportType            = $config['transport_type'];
		
		$this->baseUrl					        = 'http://'.$this->host.':'.$this->port.'/'.$this->subUrl;				

    $className                      = ucfirst($this->transportType).'RestService';
    $this->transport                = RESTServiceAbstract::factory($className, array($this->contentType));

	}
	
  /**
   * Validate options from a given array 
   * Merge with the default configuration
   * @param array $config - Associative array for configuration
   * @return array $config cleaned up
   */
  private function validateParams($config) {
    
    $config = array_merge($this->defaultConfig, $config);
    
    if(empty($config['username'])) { 
      throw new ServiceException(ServiceException::EMPTY_API_USERNAME);
    }
    
    if(empty($config['key'])) {
      throw new ServiceException(ServiceException::EMPTY_API_KEY);
    }

    try {
      $class = new ReflectionClass($config['transport_type'].'RestService');
    }
    catch(ReflectionException $e) {
      throw new ReflectionException(ServiceException::TRANSPORT_NOT_FOUND);
    }
    
    if(!$class->implementsInterface('RestServiceInterface')) {
      $config['transport_type'] = $this->defaultConfig['transport_type'];
    }

 	  return $config;
  } 
  	
	public function get($var) {
		return $this->$var;
	}
	
	public function set($var,$val) {
		$this->$var = $val;
		return $this;
	}	
}
