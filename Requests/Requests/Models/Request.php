<?php

namespace Requests\Models;

use Requests\Exceptions\RequestException;
use Requests\Exceptions\RequestTimeoutException;
use Requests\Exceptions\RequestResolveHostException;
use Requests\Models\Methods;

/**
 * Request handles the parameters and makes the efective request
 *
 * @author agustin
 */
class Request {

  protected $url = '';
  protected $method = '';
  
  static protected $default_options =  array(
      CURLOPT_HEADER => TRUE,
      CURLOPT_FRESH_CONNECT => TRUE,
      CURLOPT_RETURNTRANSFER => TRUE, //Returns the content of curl exec instead of put directly as output
      CURLINFO_HEADER_OUT => TRUE,
      //CURLOPT_COOKIEJAR => '/tmp/gus',
      //CURLOPT_COOKIEFILE => '/tmp/gus',
  );

  /**
   * Creates a Request Object
   * 
   * @param String $method A valid HTTP Method
   * @param String $url A valid Url
   * @param Array $options, posible entries
   *   params: Array of parameters to by added as Query String to the Url
   *   data: Array of data to include in the body of the request
   *   headers: Array with custom headers
   *   timeout: Time in seconds to wait for the request, Default 30
   *   allow_redirects: True or false Default True
   *   max_redirects: Numeric, default 3
   * @throws RequestException 
   */
  public function __construct($method, $url, $options = array()) {
    $method = strtoupper($method);
    $this->url = $url;
    $this->method = $method;
    
    if (!in_array($method, Methods::getMethods())) {
      throw new RequestException('Method Not Allowed');
    }
    
    if (isset($options['params']) && is_array($options['params'])) {
      $this->params = http_build_query($options['params']);
      $this->url .= '?' . $this->params;
    }

    $this->data = '';
    if (isset($options['data']) && is_array($options['data'])) {
      $this->data = http_build_query($options['data']);
    }
    
    $this->timeout = 30;
    if (isset($options['timeout']) && is_numeric($options['timeout'])) {
      $this->timeout = $options['timeout'];
    }    
    
    $this->allow_redirects = TRUE;
    if (isset($options['allow_redirects'])) {
      $this->allow_redirects = (bool) $options['allow_redirects'];
    }
    
    $this->max_redirects = 3;
    if (isset($options['max_redirects']) && is_numeric($options['max_redirects'])) {
      $this->max_redirects = (int) $options['max_redirects'];
    }
    
    $this->headers = '';
    if (isset($options['headers']) && is_array($options['headers'])) {
      $this->headers = $options['headers'];
    } 
  }

  /**
   * Execute the request
   * 
   * @return \Requests\Models\Response
   * @throws RequestException 
   */
  public function send() {
    $ch = curl_init();    
    $options = $this->setOptions();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    
    if (curl_errno($ch) > 0) {
      throw $this->createException(curl_errno($ch), curl_error($ch));
    }
    
    $response = new Response($result, curl_getinfo($ch));
    curl_close($ch);    
    return $response;
  }
  
  /**
   * Sets the Curl Options 
   * 
   * @see Curl Options http://www.php.net/manual/es/function.curl-setopt.php
   * 
   * @return array : The array with all needed curl options to be used with 
   *                 curl_setopt_array 
   */
  protected function setOptions() {
    $options = self::$default_options;
    $this->setOptionUrl($options)
        ->setOptionMethod($options)  
        ->setOptionTimeOut($options)
        ->setOptionAllowRedirect($options);
    return $options;
  }

  protected function setOptionUrl(&$options) {
    $options[CURLOPT_URL] = $this->url;
    return $this;
  }

  protected function setOptionMethod(&$options) {
    switch ($this->method) {
      case Methods::GET:
        $options[CURLOPT_POST] = FALSE;
        break;
      case Methods::POST:
        $options[CURLOPT_POST] = TRUE;        
        $this->setOptionData($options);
        break;
      default:
        $options[CURLOPT_CUSTOMREQUEST] = $this->method;
        $this->setOptionData($options);
    }
    return $this;
  }

  protected function setOptionTimeOut(&$options) {
    $options[CURLOPT_TIMEOUT] = 30;
    if (isset($this->timeout)) {
      $options[CURLOPT_TIMEOUT] = $this->timeout;
    }
    return $this;
  }

  protected function setOptionData(&$options) {
    if (isset($this->data)) {
      $options[CURLOPT_POSTFIELDS] = $this->data;
    }
    return $this;
  }
  
  protected function setOptionAllowRedirect(&$options) {
    $options[CURLOPT_FOLLOWLOCATION] = $this->allow_redirects;
    $options[CURLOPT_MAXREDIRS] = $this->max_redirects;
  }
  
  /**
   * Creates the Exception depending on the error Number
   * 
   * @param integer $errorNro 
   * @param string $message
   * @return \Requests\Exceptions\RequestException or child
   */
  protected function createException($errorNro, $message) {    
    if ($errorNro === 6) {
      return new RequestResolveHostException($message);
    }
    
    if ($errorNro === 28) {
      return new RequestTimeoutException($message);
    }
    
    return new RequestException($message);
  }
}
