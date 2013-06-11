<?php
/**
 * This file is part of the domaintoolsAPI_php_wrapper package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 /**
    @example of call returning a DomaintoolsResponse Object :

    // configure the request
    $request      = new DomaintoolsAPI();
    $response     = $request->from('whois')
                          ->domain('domaintools.com')
                          ->execute();

    // send request to the response object
    $response     = new DomaintoolsAPIResponse($request);

    // return responses
    echo $response->whois->date;
    echo $response->toXml();

 */

namespace Domaintools;

class Response 
{

    /**
     * Request object for API
     */
    protected $request;

    /**
     * Json string representing the response
     */
    protected $json;

    /**
     * Json object representation
     */
    protected $jsonObject;

    /**
     * Constructs the DomaintoolsAPIResponse object
     * @param DomaintoolsAPI $request the request object
     */
    public function __construct($request = null) {

        if(!$request instanceof Domaintools) {
            throw new Exception\ServiceException(Exception\ServiceException::INVALID_REQUEST_OBJECT);
        }
        $this->mergeJson($request->getRawResponse());
        $this->request = $request;
    }

    /**
     * Magic get method to create an alias :
     * $this->history <=> $this->jsonObject->response->history
     * if      $this->history already exists                => return it
     * elseif  $this->jsonObject->response->history exists  => return it
     * else                                                 => return null
     */
    public function __get($name) {
        if(isset($this->$name)) {
            return $this->$name;
        }
        elseif($this->jsonObject->response->$name) {
            return $this->jsonObject->response->$name;
        }
        return null;
    }

    /**
     * declare $this->json and $this->jsonObject only if json_decode worked
     * otherwise we keep the old values
     * @param string $json
     */
    public function mergeJson($json) {

        $object = json_decode($json);

        if($object === null) {
            throw new Exception\ServiceException(Exception\ServiceException::INVALID_JSON_STRING);
        }

        $this->json       = $json;
        $this->jsonObject = $object;
    }

    /**
     * Getter of the request object (DomaintoolsAPI)
     */
    public function getRequest() {
        return $this->request;
    }
}

