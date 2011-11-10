<?php

require_once __DIR__.'/../DomaintoolsAPIResponse.class.php';
require_once __DIR__.'/../DomaintoolsAPI.class.php';
require_once __DIR__.'/../DomaintoolsAPIConfiguration.class.php';

class DomaintoolsResponseTest extends PHPUnit_Framework_TestCase {
    /**
     * Init a transport
     */
    protected function getTransport($url, $fixture_path, $status = 200) {
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
}

