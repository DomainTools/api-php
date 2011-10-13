<?php
require __DIR__.'/../DomaintoolsAPI_class.inc.php';

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
          ->will($this->returnValue(file_get_contents(__DIR__.'/fixtures/domain-profile/domaintools.com/good.json')));
    
    $request->setTransport($transport);
    try {
      $response  = $request->execute();
    } catch(Exception $e) {
      return;
    }
  }
  
  /**
   * Checks default ServiceName is taken when no serviceName given
   */
  public function testDefaultServiceCalledWhenEmptyServiceName() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    $this->assertTrue($request->getDefaultServiceName() == $request->getServiceName());
  }

  /**
   * Checks ServiceException raised if unknown serviceName
   */
  public function testServiceExceptionIfUnknownServiceName() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    try {
      $request->from('unknwonService');
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::UNKNOWN_SERVICE_NAME);
    }
  }
  
  /**
   * Checks ServiceException raised if unknown returnType
   */
  public function testServiceExceptionIfUnknownReturnType() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    try {
      $request->withType('unknownType');
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::UNKNOWN_RETURN_TYPE);
    }
  }
  
  /**
   * Checks ServiceException raised if invalid domain
   */
  public function testServiceExceptionIfInvalidDomain() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    try {
      $request->domain('invalidDomain');
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::INVALID_DOMAIN);
    }
  } 
  
  /**
   * Checks ServiceException raised if invalid options
   */
  public function testServiceExceptionIfInvalidOptions() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    try {
      $request->where('invalidOptions');
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::INVALID_OPTIONS);
    }
  }
  
  /**
   * Checks username and key are (really) added to options
   */
  public function testAddCredentialsForUnsecureAuthentication() {
    
    $config = parse_ini_file(__DIR__.'/../api.ini');
    $configuration = new DomaintoolsAPIConfiguration($config);
    $configuration->set('secureAuth', false);

    $request = new DomaintoolsAPI($configuration);    
    $request->addCredentialsOptions();

    $options = $request->getOptions();
    
    $this->assertTrue(
      $config['username'] == $options['api_username'] &&
      $config['key']      == $options['api_key']
    );
  }
  
  /**
   * Checks ServiceException raised if domain call required
   */
  public function testServiceExceptionIfDomainCallRequired() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    
    try {
      $request->from('domain-profile')
              ->domain('66.249.17.251')
              ->execute();
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::DOMAIN_CALL_REQUIRED);
    }
  }
  
  /**
   * Checks ServiceException raised if ip call required
   */
  public function testServiceExceptionIfIpCallRequired() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    try {
      $request->from('host-domains')
              ->domain('domaintools.com')
              ->execute();
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::IP_CALL_REQUIRED);
    }
  }
  
  /**
   * Checks ServiceException raised if empty call required
   */
  public function testServiceExceptionIfEmptyCallRequired() {
    
    $configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');
    $request = new DomaintoolsAPI($configuration);
    try {
      $request->from('registrant-alert')
              ->domain('domaintools.com')
              ->execute();
    } catch (ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::EMPTY_CALL_REQUIRED);
    }
  }        
}
