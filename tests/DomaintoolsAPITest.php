<?php
require_once dirname(__FILE__).'/../DomaintoolsAPI.class.php';

class DomaintoolsAPITest extends PHPUnit_Framework_TestCase {

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
   * Checks the transport object is really called
   */
    public function testTransportCallsOnGet() {

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');

        $request = new DomaintoolsAPI($configuration);

        $request->withType('xml')
                ->from('domain-profile')
                ->domain('domaintools.com');

        $transport = $this->getMock('RestServiceInterface');

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

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

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
     */
    public function testServiceExceptionIfInvalidOptions() {

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);
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

        $config        = parse_ini_file(dirname(__FILE__).'/../api.ini');
        $configuration = new DomaintoolsAPIConfiguration($config);
        $configuration->set('secureAuth', false);

        $request       = new DomaintoolsAPI($configuration);
        $request->addCredentialsOptions();

        $options       = $request->getOptions();

        $this->assertTrue(
            $config['username'] == $options['api_username'] &&
            $config['key']      == $options['api_key']
        );
    }

    /**
     * Checks BadRequestException raised for status code 400
     */
    public function testBadRequestExceptionRaised() {

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/400.json';
        $transport     = $this->getTransport($url, $fixture_path, 400);

        $request->setTransport($transport);

        try {
            $request->execute();
        }
        catch(Exception $e) {
            $this->assertTrue($e instanceof BadRequestException);
        }
    }

    /**
     * Checks NotAuthorizedException raised for status code 403
     */
    public function testNotAuthorizedExceptionRaised() {

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/403.json';
        $transport     = $this->getTransport($url, $fixture_path, 403);

        $request->setTransport($transport);

        try {
            $request->execute();
        }
        catch(Exception $e) {
            $this->assertTrue($e instanceof NotAuthorizedException);
        }
    }

    /**
     * Checks NotFoundException raised for status code 404
     */
    public function testNotFoundExceptionRaised() {

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/404.json';
        $transport     = $this->getTransport($url, $fixture_path, 404);

        $request->setTransport($transport);

        try {
            $request->execute();
        }
        catch(Exception $e) {
            $this->assertTrue($e instanceof NotFoundException);
        }
    }

    /**
     * Checks InternalServerErrorException raised for status code 500
     */
    public function testInternalServerErrorExceptionRaised() {

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/500.json';
        $transport     = $this->getTransport($url, $fixture_path, 500);

        $request->setTransport($transport);

        try {
            $request->execute();
        }
        catch(Exception $e) {
            $this->assertTrue($e instanceof InternalServerErrorException);
        }
    }

    /**
     * Checks ServiceUnavailableException raised for status code 503
     */
    public function testServiceUnavailableExceptionRaised() {

        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->withType('json')->domain('domaintools.com');

        $url           = $request->debug();
        $fixture_path  = dirname(__FILE__).'/fixtures/errors/503.json';
        $transport     = $this->getTransport($url, $fixture_path, 503);

        $request->setTransport($transport);

        try {
            $request->execute();
        }
        catch(Exception $e) {
            $this->assertTrue($e instanceof ServiceUnavailableException);
        }
    }

    /**
     * Checks toJson() sets 'json' as returnType
     */
    public function testToJsonSetsJsonAsReturnType() {
        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->toJson();
        $this->assertTrue($request->getReturnType()=='json');
    }

    /**
     * Checks toXml() sets 'xml' as returnType
     */
    public function testToXmlSetsXmlAsReturnType() {
        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->toXml();
        $this->assertTrue($request->getReturnType()=='xml');
    }

    /**
     * Checks toHtml() sets 'html' as returnType
     */
    public function testToHtmlSetsHtmlAsReturnType() {
        $configuration = new DomaintoolsAPIConfiguration(dirname(__FILE__).'/../api.ini');
        $request       = new DomaintoolsAPI($configuration);

        $request->toHtml();
        $this->assertTrue($request->getReturnType()=='html');
    }
}

