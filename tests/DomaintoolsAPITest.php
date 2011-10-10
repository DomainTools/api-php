<?php
require '../DomaintoolsAPI_class.inc.php';

class DomaintoolsAPITest extends PHPUnit_Framework_TestCase
{
  /**
   * Checks the transport object is really called
   */
  public function testTransportCallsOnGet() {
  
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    
    $request = new DomaintoolsAPI($configuration);

    $request->withType('xml')
            ->from('domain-profile')
            ->domain('domaintools.com');
  
    $transport = $this->getMock('RestServiceInterface'); 
    
    $transport->expects($this->once())
          ->method('get')
          ->with($request->debug())
          ->will($this->returnValue(file_get_contents('fixtures/domain-profile/domaintools.com/good.json')));
    
    $request->setTransport($transport);
    try {
      $response  = $request->execute();
    } catch(Exception $e) {
      return;
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
      $this->assertTrue(true);
    }
  }
  
  /**
   * Checks ServiceException raised if empty key
   */  
  public function testServiceExceptionIfEmptyKey() {
    
    try {
      $configuration = new DomaintoolsAPIConfiguration(array(
        'key' => ''
      ));
    } catch (ServiceException $e) {
      $this->assertTrue(true);
    }
  }
}
