<?php
//require_once dirname(__FILE__).'/../Domaintools.class.php';

namespace Domaintools;

class DomaintoolsTest extends \PHPUnit_Framework_TestCase 
{

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
   * Checks the transport object is really called
   * @expectedException Domaintools\Exception\ServiceException
   * @expectedExceptionMessage Response is empty
   */
    public function testTransportCallsOnGet() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");

        $request = new Domaintools($configuration);

        $request->withType('xml')
                ->from('domain-profile')
                ->domain('domaintools.com');

        $transport = $this->getMock('Domaintools\Rest\ServiceInterface');

        $transport->expects($this->once())
              ->method('get')
              ->with($request->debug())
              ->will($this->returnValue(file_get_contents(dirname(__FILE__).'/fixtures/domain-profile/domaintools.com/good.json')));

        $request->setTransport($transport);
        try {
            $response  = $request->execute();
        } catch(Exception $e) {
            return;
        }
    }

    /**
     * Checks ServiceException raised if unknown returnType
     */
    public function testJsonCallIfUnknownReturnType() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->withType('unknownType')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/domain-profile/domaintools.com/good.json';
        $transport     = $this->getTransport($url, $fixture_path, 200);

        $request->setTransport($transport);

        $json          = $request->execute();

        $this->assertTrue(is_array(json_decode($json,true)));
    }

    /**
     * Checks ServiceException raised if invalid options
     * @expectedException Domaintools\Exception\ServiceException
     */
    public function testServiceExceptionIfInvalidOptions() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);
        try {
            $request->where('invalidOptions');
        } catch (Exception $e) {
            $this->assertTrue($e->getMessage() == Exception\ServiceException::INVALID_OPTIONS);
        }
    }

    /**
     * Checks username and key are (really) added to options
     */
    public function testAddCredentialsForUnsecureAuthentication() {

        $config        = parse_ini_file(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $configuration = new Configuration($config);
        $configuration->set('secureAuth', false);

        $request       = new Domaintools($configuration);
        $request->addCredentialsOptions();

        $options       = $request->getOptions();

        $this->assertTrue(
            $config['username'] == $options['api_username'] &&
            $config['key']      == $options['api_key']
        );
    }

    /**
     * Checks BadRequestException raised for status code 400
     * @expectedException Domaintools\Exception\BadRequestException
     */
    public function testBadRequestExceptionRaised() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/400.json';
        $transport     = $this->getTransport($url, $fixture_path, 400);

        $request->setTransport($transport);

            $request->execute();
    }

    /**
     * Checks NotAuthorizedException raised for status code 403
     * @expectedException Domaintools\Exception\NotAuthorizedException
     */
    public function testNotAuthorizedExceptionRaised() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/403.json';
        $transport     = $this->getTransport($url, $fixture_path, 403);

        $request->setTransport($transport);

        $request->execute();
    }

    /**
     * Checks NotFoundException raised for status code 404
     * @expectedException Domaintools\Exception\NotFoundException
     */
    public function testNotFoundExceptionRaised() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/404.json';
        $transport     = $this->getTransport($url, $fixture_path, 404);

        $request->setTransport($transport);

        $request->execute();
    }

    /**
     * Checks InternalServerErrorException raised for status code 500
     * @expectedException Domaintools\Exception\InternalServerErrorException
     */
    public function testInternalServerErrorExceptionRaised() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/500.json';
        $transport     = $this->getTransport($url, $fixture_path, 500);

        $request->setTransport($transport);

        $request->execute();
    }

    /**
     * Checks ServiceUnavailableException raised for status code 503
     * @expectedException Domaintools\Exception\ServiceUnavailableException
     */
    public function testServiceUnavailableExceptionRaised() {

        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/503.json';
        $transport     = $this->getTransport($url, $fixture_path, 503);

        $request->setTransport($transport);

        $request->execute();
    }

    /**
     * Checks toJson() sets 'json' as returnType
     */
    public function testToJsonSetsJsonAsReturnType() {
        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->toJson();
        $this->assertTrue($request->getReturnType()=='json');
    }

    /**
     * Checks toXml() sets 'xml' as returnType
     */
    public function testToXmlSetsXmlAsReturnType() {
        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->toXml();
        $this->assertTrue($request->getReturnType()=='xml');
    }

    /**
     * Checks toHtml() sets 'html' as returnType
     */
    public function testToHtmlSetsHtmlAsReturnType() {
        $configuration = new Configuration(dirname(__FILE__)."/../src/Domaintools/api.ini");
        $request       = new Domaintools($configuration);

        $request->toHtml();
        $this->assertTrue($request->getReturnType()=='html');
    }
}

