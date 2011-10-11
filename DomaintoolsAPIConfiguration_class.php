<?php
/*
* This file is part of the domaintoolsAPI_php_wrapper package.
*
* Copyright (C) 2011 by domaintools.com - EuroDNS S.A. 
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
class DomaintoolsAPIConfiguration {

	/*
	 * Server Host
	 */
	private $host;
	/*
	 * Server Port
	 */
	private $port;
	/*
	 * Sub URL (version)
	 */
	private $subUrl;
	/*
	 * Complete URL
	 */
	private $baseUrl;
	/*
	 * Domaintools API Username
	 */
	private $username;
	/*
	 * Domaintools API Password
	 */
	private $password;
	/*
	 * (Boolean) force to secure authentication
	 */
	private $secureAuth;	
	/*
	 * Default return type (json/xml/html)
	 */
	private $returnType;
	
	/*
	 * Object in charge of calling the API
	 */
	private $transport;
	
	/**
   * Construct of the class and initiliaze with default values
   * @param $serviceName
   */
	public function __construct($ini_resource = '') {
	
	  if(empty($ini_resource)) $ini_resource = __DIR__.'/api.ini';

	  if(!is_array($ini_resource) && file_exists($ini_resource)) {
      $config = parse_ini_file($ini_resource);
    }
    elseif(is_array($ini_resource)) { 
	    $config = $ini_resource;
	  }
	  
	  $this->init($config);
  }   
  
  /**
   * Initialize the configuration Object
   * @param $config - Associative array for configuration
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
		
		$this->baseUrl					        = $this->host.':'.$this->port.'/'.$this->subUrl;				

    $className                      = ucfirst($config['transport']).'RestService';
    $class                          = new ReflectionClass($className);
    
    if($class->implementsInterface('RestServiceInterface')) {
      $this->transport              = RESTServiceAbstract::factory($className, array($this->contentType));
    }
    else {
      $className                    = ucfirst($defaults['transport']).'RestService';
      $this->transport              = RESTServiceAbstract::factory($className, array($this->contentType));
    }
	}
	
  /**
   * Validate options from a given array 
   * Merge with the default configuration
   * @param $config - Associative array for configuration
   * @return Same array cleaned up
   */
  private function validateParams($config) {

    $defaults = array(
      'username'     => '',
      'key'          => '',
      'host'         => 'api.domaintools.com',
      'port'         => '80',
      'version'      => 'v1',
      'secure_auth'  => true,
      'return_type'  => 'json',
      'transport'    => 'curl',
      'content_type' => 'application/json'
    );
    
    $config = array_merge($defaults, $config);
    
    if(empty($config['username'])) { 
      throw new ServiceException('Username missing. Must be set in your api.ini');
    }
    
    if(empty($config['key'])) { 
      throw new ServiceException('Key missing. Must be set in your api.ini');
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
