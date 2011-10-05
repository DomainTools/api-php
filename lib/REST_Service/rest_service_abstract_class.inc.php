<?php

/*
* This file is part of the domainAPI_php_wrapper package.
*
* Copyright (C) 2011 by domainAPI.com - EuroDNS S.A. 
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'rest_service_interface_class.inc.php');

/**
  * Abstract class for the REST Services
  * http://en.wikipedia.org/wiki/HyperText_Transfer_Protocol
  */
abstract class RESTServiceAbstract implements RESTServiceInterface
{
  protected $options;
  protected $contentType;
  
	/*
	 * SEND
	 * Generic method to send requests to a specific URL
	 */
	abstract protected function send($method, $url, $dataArr = array());
	
	/*
	 * GET
	 * Requests a representation of the specified resource
	 */	
	public function get($url)
	{
		return $this->send('GET', $url);
	}
		
	/*
	 * HEAD
	 * Requests a representation of the specified resource 
	 * Identical to the one that would correspond to a GET request, but without the response body
	 */	
	public function head($url)
	{
		return $this->send('HEAD', $url);
	}

	/*
	 * POST
	 * Submits data to be processed to the identified resource
	 */	
	public function post($url, $dataArr)
	{
		return $this->send('POST', $url, $dataArr);
	}
	
	/*
	 * PUT
	 * Uploads a representation of the specified resource.
	 */	
	public function put ($url, $dataArr)
	{
		$data = $this->send('PUT', $url, $dataArr);
		return $data;
	}
	
	/*
	 * DELETE
	 * Deletes the specified resource
	 */
	public function delete ($url)
	{
		return $this->send('DELETE', $url);
	}
	
	/*
	 * OPTIONS
	 * Returns the HTTP methods that the server supports for specified URL
	 * This can be used to check the functionality of a web server by requesting '*' instead of a specific resource
	 */
	public function options ($url)
	{
		return $this->send('OPTIONS', $url);
	}
	
	/*
	 * TRACE
	 * Echoes back the received request, so that a client can see what intermediate servers are adding or changing in the request.
	 */
	public function trace ($url)
	{
		return $this->send('TRACE', $url);
	}
	
	/*
	 * CONNECT
	 * Converts the request connection to a transparent TCP/IP tunnel,
	 */
	public function connect ($url)
	{
		return $this->send('CONNECT', $url);
	}	
	
	/*
	 * SETOPTION
	 * Set a specific CURL option
	 */
	public function setOption ($key, $value)
	{
		$this->options[$key] = $value;
	}
	
	/*
	 * DROPOPTION
	 * Drop a specific CURL option
	 */
	public function dropOption ($key)
	{
		if (isset ($this->options[$key]))
		{
			unset($this->options[$key]);
		}
	}
	
	/*
	 * SETCONTENTTYPE
	 * Set a specific content-type
	 */
	public function setContentType($value)
	{
		$this->contentType = $value;
	}			
}

?>
