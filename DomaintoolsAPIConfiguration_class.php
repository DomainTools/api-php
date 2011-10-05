<?php
/*
* This file is part of the domaintoolsAPI_php_wrapper package.
*
* Copyright (C) 2011 by domaintools.com - EuroDNS S.A. 
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
class DomaintoolsAPIConfiguration{

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
	public function __construct(){

	  // defaults values
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
	  
	  $api                            = parse_ini_file(dirname(__FILE__).'/api.ini');
	  if(empty($api))            $api = array();
	  $api                            = array_merge($defaults, $api);
	  
		$this->host 					          = $api['host'];
		$this->port						          = $api['port'];
		$this->subUrl					          = $api['version'];
		$this->username					        = $api['username'];
		$this->password					        = $api['key'];
		$this->secureAuth               = $api['secure_auth'];
		$this->returnType				        = $api['return_type'];
		$this->contentType				      = $api['content_type'];
		
		$this->baseUrl					        = $this->host.':'.$this->port.'/'.$this->subUrl;				
		
		
		$class = ucfirst($api['transport']).'RestService';

    try{
      $this->transport              = new $class;
    } catch(Exception $e){
      $this->transport              = new ucfirst($defaults['transport']).'RestService';
    }
	}
	
	public function validateOptions(){
	
	}
	
	public function get($var){
		return $this->$var;
	}
	
	public function set($var,$val){
		$this->$var = $val;
		return $this;
	}	
}
?>
