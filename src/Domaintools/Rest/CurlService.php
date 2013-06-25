<?php

/**
 * This file is part of the domaintoolsAPI_php_wrapper package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Domaintools\Rest;

/**
 * REST over CURL Implementation
 *
 * Usage:
 * 	require("REST_Service/curl_rest_service_class.inc.php");
 * 	$curlRestService = new CurlRestService();
 * 	echo $curlRestService->get('http://www.domaintools.com');
 */
final class CurlService extends ServiceAbstract 
{

    private $lastInfo;

	/**
	 * options
	 * Default CURL options
	 */
	public function __construct($content_type, $options = array())	{
	    $defaults = array(
		    'CURLOPT_CUSTOM_HTTPHEADER' => '',
		    'CURLOPT_CUSTOM_RAWDATA' => '',
		    'CURLOPT_HEADER' => 0,
		    'CURLOPT_USERAGENT' => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
		    'CURLOPT_VERBOSE' => 0,
		    'CURLOPT_FOLLOWLOCATION' => 0,
		    'CURLOPT_RETURNTRANSFER' => 1,
		    'CURLOPT_TIMEOUT' => 10
	    );
	    parent::__construct($content_type, array_merge($defaults, $options));
	}

	/**
	 * SEND
	 * Generic method to send requests to a specific URL
	 */
	protected function send($method, $url, $dataArr = array()) {
		if (($cUrl = curl_init($url)) == false)	{
		 	throw new \Exception("CURL_INIT ERROR [".$url." | ".curl_error($cUrl)."]");
		}

		curl_setopt($cUrl, CURLOPT_URL, $url);
		curl_setopt($cUrl, CURLOPT_CUSTOMREQUEST, $method);
		foreach ($this->options AS $constant => $value) {
			if (defined ($constant)) {
				curl_setopt($cUrl, constant ($constant), $value);
			}
		}

		if (in_array(strtoupper($method), array('POST','PUT')) AND is_array ($dataArr))	{
			$data = '';

			if (! empty ($this->options['CURLOPT_CUSTOM_RAWDATA']) AND $this->options['CURLOPT_CUSTOM_RAWDATA'] == true) {
				$data = http_build_query($dataArr);
			}
			else {
				foreach ($dataArr AS $key => $value) {
					$data .= $key.'='.$value.'&';
				}
			}

			if ( ! empty ($this->options['CURLOPT_CUSTOM_HTTPHEADER']))	{
				curl_setopt($cUrl, CURLOPT_HTTPHEADER, array($this->options['CURLOPT_CUSTOM_HTTPHEADER'] ,'Content-Length: '.strlen($data)));
			}
			else {
				curl_setopt($cUrl, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($data)));
			}
			curl_setopt($cUrl, CURLOPT_POSTFIELDS, $data);
		}
		if (in_array(strtoupper($method), array('POST','PUT')) AND !is_array ($dataArr)) {

		    $data = $dataArr ;
			if ( ! empty ($this->options['CURLOPT_CUSTOM_HTTPHEADER'])) {

				curl_setopt($cUrl, CURLOPT_HTTPHEADER, array($this->options['CURLOPT_CUSTOM_HTTPHEADER'] ,'Content-Length: '.strlen($data)));
			}
			else {
				curl_setopt($cUrl, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($data)));
			}

			curl_setopt($cUrl, CURLOPT_POSTFIELDS, $data);
		}
		if (($result = curl_exec($cUrl)) == false) {
			throw new \Exception("CURL_EXEC ERROR [".$url." | ".curl_error($cUrl)."]");
		}
		$this->lastInfo = curl_getinfo($cUrl);
		//Close connection
		curl_close($cUrl);
		//Return result
		return $result;
	}

	public function getInfo() {
	    return $this->lastInfo;
	}

	public function getStatus() {
	  return $this->lastInfo['http_code'];
	}
}
