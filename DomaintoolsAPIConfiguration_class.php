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
	
	/**
     * Construct of the class and initiliaze with default values
     * @param $serviceName
     */
	public function __construct(){
	  $api                            = parse_ini_file(dirname(__FILE__).'/api.ini');
		$this->host 					          = 'api.domaintools.com';
		$this->port						          = '80';
		$this->subUrl					          = 'v1';
		$this->baseUrl					        = $this->host.':'.$this->port.'/'.$this->subUrl;		
		$this->username					        = $api['username'];
		$this->password					        = $api['key'];
		$this->secureAuth               = true;
		$this->returnType				        = 'json';
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
