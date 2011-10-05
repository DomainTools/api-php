<?php

/*
* This file is part of the domainAPI_php_wrapper package.
*
* Copyright (C) 2011 by domainAPI.com - EuroDNS S.A. 
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
  * Interface for the REST Services
  * http://en.wikipedia.org/wiki/HyperText_Transfer_Protocol
  */
interface RESTServiceInterface
{
	/*
	 * GET
	 * Requests a representation of the specified resource
	 */
	public function get($url);
	
	/*
	 * HEAD
	 * Requests a representation of the specified resource 
	 * Identical to the one that would correspond to a GET request, but without the response body
	 */
	public function head($url);
	
	/*
	 * POST
	 * Submits data to be processed to the identified resource
	 */	
	public function post($url, $dataArr);
	
	/*
	 * PUT
	 * Uploads a representation of the specified resource
	 */	
	public function put($url, $dataArr);
	
	/*
	 * DELETE
	 * Deletes the specified resource
	 */
	public function delete($url);
	
	/*
	 * OPTIONS
	 * Returns the HTTP methods that the server supports for specified URL
	 * This can be used to check the functionality of a web server by requesting '*' instead of a specific resource
	 */
	public function options($url);	
	
	/*
	 * TRACE
	 * Echoes back the received request, so that a client can see what intermediate servers are adding or changing in the request.
	 */
	public function trace($url);
	
	/*
	 * CONNECT
	 * Converts the request connection to a transparent TCP/IP tunnel,
	 */
	public function connect($url);
	
	/*************** ADDITIONAL METHODS ***********************/
	
	/**
	 * Returns the status of a request
	 */
	public function getStatus();
}
?>
