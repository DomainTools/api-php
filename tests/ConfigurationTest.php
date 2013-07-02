<?php

//require_once dirname(__FILE__).'/../Configuration.class.php';

namespace Domaintools;

class ConfigurationTest extends \PHPUnit_Framework_TestCase 
{

    /**
     * Checks the api.ini (default config file) is called
     * if nothing is given to the Configuration constructor
     */
    public function testDefaultConfigCalledIfNoneGiven() 
    {
        $defaultConfigPath = realpath(dirname(__FILE__)."/../src/Domaintools/api.ini");
        
        $configuration = new Configuration();

        $this->assertTrue($defaultConfigPath == $configuration->get('defaultConfigPath'));
    }

    /**
     * Checks Service exception raised if the default config ini file do not exist
     * @expectedException Domaintools\Exception\ServiceException
     * @expectedExceptionMessage Config file does not exist
     */
    public function testServiceExceptionIfConfigFileDoNotExist() {
        $configuration = new Configuration('invalidPath');
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
        $configuration = new Configuration($config);

        $this->assertTrue($configuration->get('username')==$config['username']);
        $this->assertTrue($configuration->get('password')==$config['key']);
    }

    /**
     * Checks NotAuthorizedException raised if empty username
     * @expectedException Domaintools\Exception\NotAuthorizedException
     * @expectedExceptionMessage Empty API username
     */
    public function testNotAuthorizedExceptionIfEmptyUsername() {
        $configuration = new Configuration(array(
            'username' => ''
        ));
    }

    /**
     * Checks NotAuthorizedException raised if invalid credentials
     * @expectedException Domaintools\Exception\NotAuthorizedException
     * @expectedExceptionMessage Empty API key
     */
    public function testNotAuthorizedExceptionIfInvalidCredentials() {
        $configuration = new Configuration(array(
            'username' => 'username',
            'key' => ''
        ));
    }

    /**
     * Checks the default transport is called if an invalid one is given
     */
//    public function testDefaultTransportCalledIfCurrentOneNotValid() {
//
//        include dirname(__FILE__).'/fixtures/FakeRestService.php';
//
//        $configuration = new Configuration(array(
//            'username'       => 'username',
//            'key'            => 'password',
//            'transport_type' => 'fake'
//        ));
//
//        $defaultConfig = $configuration->get('defaultConfig');
//        $this->assertTrue($defaultConfig['transport_type'] == $configuration->get('transportType'));
//    }


    /**
     * Checks ReflectionException raised if transport not found
     */
    public function testReflectionExceptionIfTransportNotFound() {

        try {
            $configuration = new Configuration(array(
                'username'       => 'username',
                'key'            => 'password',
                'transport_type' => 'bad'
            ));
        } catch (\ReflectionException $e) {
            $this->assertTrue($e->getMessage() == Exception\ServiceException::TRANSPORT_NOT_FOUND);
        }
    }
}

