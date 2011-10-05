<?php
class DomaintoolsTest extends PHPUnit_Framework_TestCase
{
 
  public function provider()
  {
    return array(
      array('domain-profile','domaintools.com', 'xml'),
      array('whois','domaintools.com', 'xml'),
      array('whois-history','domaintools.com', 'xml')
    );
  }
  /**
   * @dataProvider provider
   */  
  public function test($serviceName, $domain, $format)
  {
    require_once '../DomaintoolsAPI_class.inc.php';
    
    $configuration = new DomaintoolsAPIConfiguration();  
    $api           = new DomaintoolsAPI($serviceName, $configuration);
    $response      = $api->withType($format)->get($domain);
    
    $infos         = $configuration->get('transport')->getInfo();
    
    $this->assertEquals(200, $infos['http_code']);
  }
}
