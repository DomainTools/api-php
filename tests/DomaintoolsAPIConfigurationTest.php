<?php

class DomaintoolsAPIConfigurationTest extends PHPUnit_Framework_TestCase
{
  /**
   * Checks the api.ini (default config file) is called 
   * if nothing is given to the DomaintoolsAPIConfiguration constructor
   */
  public function testDefaultConfigCalledIfNoneGiven() {
  
    $defaultConfigPath = realpath("api.ini");
    $configuration = new DomaintoolsAPIConfiguration();
    
    $this->assertTrue($defaultConfigPath == $configuration->get('defaultConfigPath'));
  }
  
  /**
   * Checks Service exception raised if the default config ini file do not exist
   */
  public function testServiceExceptionIfConfigFileDoNotExist() {
  
    try {
      $configuration = new DomaintoolsAPIConfiguration('invalidPath');
    } 
    catch (Exception $e) {
      $this->assertTrue($e->getMessage() == ServiceException::INVALID_CONFIG_PATH); 
    }
  }
  
  /**
   * Checks the (good) merging of default configuration with the user one
   * If parameters are missing on the user configuration then default parameters complete the configuration
   */
  public function testMergeWithDefaultConfiguration() {
  
    $config = array(
      'username' => 'krispouille', 
      'key'      => 'password'
    );
    
    $configuration = new DomaintoolsAPIConfiguration($config);

    $this->assertTrue($configuration->get('username')==$config['username']);
    $this->assertTrue($configuration->get('password')==$config['key']);
  }

  /**
   * Checks ServiceException raised if empty username
   */
  public function testServiceExceptionIfEmptyUsername() {
    
    try {
      $configuration = new DomaintoolsAPIConfiguration(array(
        'username' => ''
      ));
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::EMPTY_API_USERNAME);
    }
  }
  
  /**
   * Checks ServiceException raised if empty key
   */  
  public function testServiceExceptionIfEmptyKey() {
    
    try {
      $configuration = new DomaintoolsAPIConfiguration(array(
        'username' => 'krispouille',
        'key' => ''
      ));
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::EMPTY_API_KEY);
    }
  }
      
}
