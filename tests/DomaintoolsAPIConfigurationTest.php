<?php

require_once __DIR__.'/../DomaintoolsAPIConfiguration.class.php';

class DomaintoolsAPIConfigurationTest extends PHPUnit_Framework_TestCase {

    /**
     * Checks the api.ini (default config file) is called
     * if nothing is given to the DomaintoolsAPIConfiguration constructor
     */
    public function testDefaultConfigCalledIfNoneGiven() {

        $defaultConfigPath = realpath(__DIR__."/../api.ini");
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
            'username' => 'username',
            'key'      => 'password'
        );

        $configuration = new DomaintoolsAPIConfiguration($config);

        $this->assertTrue($configuration->get('username')==$config['username']);
        $this->assertTrue($configuration->get('password')==$config['key']);
    }

    /**
     * Checks NotAuthorizedException raised if empty username
     */
    public function testNotAuthorizedExceptionIfEmptyUsername() {

        try {
            $configuration = new DomaintoolsAPIConfiguration(array(
                'username' => ''
            ));
        } catch (Exception $e) {
            $this->assertTrue($e instanceof NotAuthorizedException);
            //$this->assertTrue($e->getMessage() == ServiceException::EMPTY_API_USERNAME);
        }
    }

    /**
     * Checks NotAuthorizedException raised if invalid credentials
     */
    public function testNotAuthorizedExceptionIfInvalidCredentials() {

        try {
            $configuration = new DomaintoolsAPIConfiguration(array(
                'username' => 'username',
                'key' => ''
            ));
        } catch (NotAuthorizedException $e) {
            $this->assertTrue($e instanceof NotAuthorizedException);
        }
    }

    /**
     * Checks the default transport is called if an invalid one is given
     */
    public function testDefaultTransportCalledIfCurrentOneNotValid() {

        include __DIR__.'/fixtures/FakeRestService.php';

        $configuration = new DomaintoolsAPIConfiguration(array(
            'username'       => 'username',
            'key'            => 'password',
            'transport_type' => 'fake'
        ));

        $defaultConfig = $configuration->get('defaultConfig');
        $this->assertTrue($defaultConfig['transport_type'] == $configuration->get('transportType'));
    }


    /**
     * Checks ReflectionException raised if transport not found
     */
    public function testReflectionExceptionIfTransportNotFound() {

        try {
            $configuration = new DomaintoolsAPIConfiguration(array(
                'username'       => 'username',
                'key'            => 'password',
                'transport_type' => 'bad'
            ));
        } catch (ReflectionException $e) {
            $this->assertTrue($e->getMessage() == ServiceException::TRANSPORT_NOT_FOUND);
        }
    }
}

