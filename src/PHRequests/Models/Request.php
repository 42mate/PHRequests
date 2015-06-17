<?php

namespace PHRequests\Models;

use PHRequests\Exceptions\PHRequestsException;
use PHRequests\Exceptions\PHRequestsTimeoutException;
use PHRequests\Exceptions\PHRequestsResolveHostException;
use PHRequests\Models\Methods;

/**
 * Request handles the parameters and makes the efective request
 *
 * @author agustin
 */
class Request extends Requester {
  
  /**
   * Creates a Request Object
   *
   * @param $options
   *
   * @see resetOptions for more detail of the options
   */
  public function __construct($options = array()) {
    $options['response_type'] = self::RESPONSE_ARRAY;
    parent::__construct($options);
  }

  /**
   * Executes the Request
   *
   * @param String $method : The HTTP method to use, by default use the internal Method.
   * @param String $url : The url to hit
   * @param Array $data : The Data to append in the body
   * @param Array $params : The Parameters to append as a Query String
   * @param Array $request_headers : Array with strings of headers directives.
   *
   * @throws \PHRequests\Exceptions\PHRequestsException
   * @return String|Boolean : The content or false on failure
   */
  public function execute($method, $url, $data = null, $params = null, $request_headers = array()) {
   
    if (!in_array($method, Methods::getMethods())) {
      throw new PHRequestsException('Method Not Allowed');
    }
   
    try {
      $responseInfo = parent::execute($method, $url, $data, $params, $request_headers);
      return new Response($responseInfo);
    } catch(\Exception $e) {
      throw $this->createException($e->getCode(), $e->getMessage());
    }
  }
  
  /**
   * Creates the Exception depending on the error Number
   * 
   * @param integer $errorNro 
   * @param string $message
   * @return \PHRequests\Exceptions\PHRequestsException or child
   */
  protected function createException($errorNro, $message) {  
    
    if ($errorNro === 6) {
      return new PHRequestsResolveHostException($message, $errorNro);
    }
    
    if ($errorNro === 28) {
      return new PHRequestsTimeoutException($message, $errorNro);
    }
    
    return new PHRequestsException($message, $errorNro);
  }
}
