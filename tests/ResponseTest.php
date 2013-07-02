<?php

//require_once dirname(__FILE__).'/../DomaintoolsResponse.class.php';
//require_once dirname(__FILE__).'/../Domaintools.class.php';
//require_once dirname(__FILE__).'/../Configuration.class.php';

namespace Domaintools;

class ResponseTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Init a transport
     */
    protected function getTransport($url, $fixture_path, $status = 200) {
        $transport = $this->getMock('Domaintools\Rest\ServiceInterface');

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
        $this->configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");

        $this->request       = new Domaintools($this->configuration);

        $this->request->domain('domaintools.com');

        $url                 = $this->request->debug();
        $fixture_path        = dirname(__FILE__).'/fixtures/domain-profile/domaintools.com/good.json';
        $transport           = $this->getTransport($url, $fixture_path);

        $this->request->setTransport($transport);
        $this->response      = $this->request->execute();
    }

    /**
     * Checks exception raised if a Domaintools instance is not given
     * @expectedException Domaintools\Exception\ServiceException
     * @expectedExceptionMessage Invalid object; DomaintoolsAPI instance required
     */
    public function testExceptionIfInvalidRequestObjectGiven() {
        $response = new Response(new \stdClass);
    }

    /**
     * Checks exception raised if a valid json string is not given
     * @zexpectedException Domaintools\Exception\ServiceException
     */
    public function testExceptionIfInvalidJsonGiven() {
        $json = 'xx}}';
        try {
            $response = new Response($this->request, $json);
        }
        catch(ServiceException $e) {
            $this->assertTrue($e->getMessage() == ServiceException::INVALID_JSON_STRING);
        }
    }

    /**
     * Checks Domaintools instance has been attached to DomaintoolsResponse
     */
    public function testRequestObjectAttachedToResponse() {

        $this->assertTrue($this->request == $this->response->getRequest());
    }
}

