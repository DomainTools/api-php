<?php
/*
* This file is part of the domaintoolstoolsAPI_php_wrapper package.
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

require_once("lib/REST_Service/curl_rest_service_class.inc.php");
require_once("exceptions/ServiceException.class.php");

class DomaintoolsAPIConfiguration {
  
  CONST DEFAULT_HOST = 'api.domaintools.com';
  CONST FREE_HOST = 'freeapi.domaintools.com';

	/**
	 * Server Host
	 */
	protected $host;

	/**
	 * Server Port
	 */
	protected $port;

	/**
	 * Sub URL (version)
	 */
	protected $subUrl;

	/**
	 * Complete URL
	 */
	protected $baseUrl;

	/**
	 * Domaintools API Username
	 */
	protected $username;

	/**
	 * Domaintools API Password
	 */
	protected $password;

	/**
	 * (Boolean) force to secure authentication
	 */
	protected $secureAuth;

	/**
	 * Default return type (json/xml/html)
	 */
	protected $returnType;

	/**
	 * transport type (curl, etc.)
	 */
	protected $transportType;

	/**
	 * Object in charge of calling the API
	 */
	protected $transport;

	/**
	 * default config file path
	 */
	protected $defaultConfigPath;

	/**
	 * default configuration
	 * (that will be use to complete if necessary)
	 */
	protected $defaultConfig = array(
	      'host'           => DomaintoolsAPIConfiguration::DEFAULT_HOST,
        'username'       => '',
        'key'            => '',
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

        $this->defaultConfigPath = dirname(__FILE__).'/api.ini';

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
    protected function init($config = array()) {

        $config               = $this->validateParams($config);

        $this->host           = $config['host'];
        $this->port           = $config['port'];
        $this->subUrl         = $config['version'];
        $this->username       = $config['username'];
        $this->password       = $config['key'];
        $this->secureAuth     = $config['secure_auth'];
        $this->returnType     = $config['return_type'];
        $this->contentType    = $config['content_type'];
        $this->transportType  = $config['transport_type'];

        $this->createBaseUrl();

        $className                      = ucfirst($this->transportType).'RestService';
        $this->transport                = RESTServiceAbstract::factory($className, array($this->contentType));
    }
    
    
    /**
     * Create the baseUrl for next requests based on current config
     * @return String baseUrl
     */
    protected function createBaseUrl() {
      $this->baseUrl = 'http://'.$this->host.':'.$this->port.'/'.$this->subUrl;
      return $this->baseUrl;
    }

    /**
     * Validate options from a given array
     * Merge with the default configuration
     * @param array $config - Associative array for configuration
     * @return array $config cleaned up
     */
    protected function validateParams($config) {

        $config = array_merge($this->defaultConfig, $config);

        /*if(empty($config['username'])) {
            throw new ServiceException(ServiceException::EMPTY_API_USERNAME);
        }

        if(empty($config['key'])) {
            throw new ServiceException(ServiceException::EMPTY_API_KEY);
        }*/

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
    /**
     * global getter
     */
    public function get($var) {
        return $this->$var;
    }

    /**
     * global setter
     */
    public function set($var,$val) {
        $this->$var = $val;
        $this->createBaseUrl();
        return $this;
    }
}
