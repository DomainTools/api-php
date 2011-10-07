<?php
require '../DomaintoolsAPI_class.inc.php';

class DomaintoolsAPITest extends PHPUnit_Framework_TestCase
{
  /**
   * Checks the transport object is really called
   */
  public function testTransportCallsOnGet() 
  {
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
    }
  }
}
