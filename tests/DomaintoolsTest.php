<?php
class DomaintoolsTest extends PHPUnit_Framework_TestCase
{
 
  public function provider()
  {
    return array(
      array('domain-profile',       'domaintools.com', 'xml'),
      array('whois',                'domaintools.com', 'xml'),
      array('whois-history',        'domaintools.com', 'xml'),
      array('hosting-history',      'domaintools.com', 'xml'),
      array('reverse-ip',           'domaintools.com', 'xml'),
      array('name-server-domains',  'domaintools.com', 'xml'),
      array('reverse-whois',        ''               , 'xml', array('terms' => 'DomainTools LLC')),
      array('domain-suggestions',   ''               , 'xml', array('query' => 'domain tools')),
      array('domain-search',        ''               , 'xml', array('query' => 'domain tools')),
      array('mark-alert',           ''               , 'xml', array('query' => 'domaintools')),
      array('registrant-alert',     ''               , 'xml', array('query' => 'domaintools'))
    );
  }
  /**
   * @dataProvider provider
   */  
  public function test($serviceName, $domain, $format, $options = array())
  {
    require_once '../DomaintoolsAPI_class.inc.php';
    
    $configuration = new DomaintoolsAPIConfiguration();  
    $api           = new DomaintoolsAPI($serviceName, $configuration);
    $response      = $api->withType($format)->where($options)->get($domain);
    
    $infos         = $configuration->get('transport')->getInfo();
    
    $this->assertEquals(200, $infos['http_code']);
  }
}
