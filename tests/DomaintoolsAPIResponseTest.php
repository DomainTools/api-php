<?php

require_once __DIR__.'/../DomaintoolsAPIResponse.class.php';
require_once __DIR__.'/../DomaintoolsAPI.class.php';
require_once __DIR__.'/../DomaintoolsAPIConfiguration.class.php';

class DomaintoolsResponseTest extends PHPUnit_Framework_TestCase {
  /**
   * Init a transport
   */
  private function getTransport($url, $fixture_path, $status = 200) {
    $transport = $this->getMock('RestServiceInterface');

    $transport->expects($this->any())
                ->method('get')
                ->with($url)
                ->will($this->returnValue(file_get_contents($fixture_path)));

    $transport->expects($this->any())
                ->method('getStatus')
                ->will($this->returnValue($status));

    return $transport;
  }

  /**
   * Set up before each test
   */
  public function setUp() {
    $this->configuration = new DomaintoolsAPIConfiguration(__DIR__.'/../api.ini');

    $this->request       = new DomaintoolsAPI($this->configuration);

    $this->request->domain('domaintools.com');

    $url                 = $this->request->debug();
    $fixture_path        = __DIR__.'/fixtures/domain-profile/domaintools.com/good.json';
    $transport           = $this->getTransport($url, $fixture_path);

    $this->request->setTransport($transport);
    $this->response      = $this->request->execute();
  }

  /**
   * Checks exception raised if a DomaintoolsAPI instance is not given
   */
  public function testExceptionIfInvalidRequestObjectGiven() {

    try {
      $response = new DomaintoolsAPIResponse(new stdClass);
    }
    catch(ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::INVALID_REQUEST_OBJECT);
    }
  }

  /**
   * Checks exception raised if a valid json string is not given
   */
  public function testExceptionIfInvalidJsonGiven() {

    $json = '';

    try {
      $response = new DomaintoolsAPIResponse($this->request, $json);
    }
    catch(ServiceException $e) {
      $this->assertTrue($e->getMessage() == ServiceException::INVALID_JSON_STRING);
    }
  }

  /**
   * Checks DomaintoolsAPI instance has been attached to DomaintoolsAPIResponse
   */
   public function testRequestObjectAttachedToResponse() {

    $this->assertTrue($this->request == $this->response->getRequest());
   }

   /**
    * Checks toJson returns a valid json string from the request object
    */
    public function testToJsonReturnsValidJson() {

      $json = $this->request->withType('json')->execute();

      $this->assertTrue($json == $this->response->toJson());

    }

    /**
     * Checks toArray returns an array representation of the response from the request object
     */
     public function testToArrayReturnsValidArray() {
      $json = $this->request->withType('json')->execute();
      $this->assertTrue($this->response->toArray() == json_decode($json,true));
     }

    /**
     * Checks toObject returns an object representation of the response from the request object
     */
     public function testToObjectReturnsValidObject() {
      $json = $this->request->withType('json')->execute();
      $this->assertTrue($this->response->toObject() == json_decode($json));
     }

    /**
     * Checks toXml returns an xml representation of the response from the request object
     */
     public function testToXmlReturnsValidXml() {
      $this->request->withType('xml');

      $url          = $this->request->debug();
      $fixture_path = __DIR__.'/fixtures/domain-profile/domaintools.com/good.xml';
      $transport    = $this->getTransport($url, $fixture_path, 200);
      $this->request->setTransport($transport);

      $xml          = $this->request->execute();
      $this->assertTrue($this->response->toXml() == $xml);
     }

    /**
     * Checks toHtml returns an html representation of the response from the request object
     */
     public function testToHtmlReturnsValidHtml() {
      $this->request->withType('html');

      $url          = $this->request->debug();
      $fixture_path = __DIR__.'/fixtures/domain-profile/domaintools.com/good.html';
      $transport    = $this->getTransport($url, $fixture_path, 200);
      $this->request->setTransport($transport);

      $html         = $this->request->execute();
      $this->assertTrue($this->response->toHtml() == $html);
     }
}

