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
   *   proxy: Array (url, auth). Default None
   *   ssl_ca: String, The path to the Cert File, if the connection is HTTPs and the CA isn't set 
   *           any CA will be a valid cert. 
   *           In order to verify you must have a valid certificate, if you don't have a valid
   *           certificate you must skip the verification.
   * @throws PHRequestsException 
   */
  public function __construct($method, $url, $options = array()) {
    $method = strtoupper($method);
    $this->url = $url;
    $this->method = $method;
    
    if (!in_array($method, Methods::getMethods())) {
      throw new PHRequestsException('Method Not Allowed');
    }
    
    if (isset($options['params']) && is_array($options['params'])) {
      $this->params = http_build_query($options['params']);
      $this->url .= '?' . $this->params;
    }

    $this->data = '';
    if (isset($options['data']) && is_array($options['data'])) {
      $this->data = $options['data'];
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
    
    $this->proxy = FALSE;
    if (isset($options['proxy']) && is_array($options['proxy'])) {
      $this->proxy = $options['proxy'];
    }
    
   $this->ssl_ca = '';
   if (isset($options['ssl_ca'])) {     
     if (file_exists($options['ssl_ca'])) {
       $this->ssl_ca = $options['ssl_ca'];
     } else {
       $this->createException('10001', 'The CA Certificate can\'t be found');
     }
   }
   
  }

  /**
   * Execute the request
   * 
   * @return \PHRequests\Models\Response
   * @throws PHRequestsException 
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
        ->setOptionProxy($options)
        ->setOptionTimeOut($options)
        ->setOptionAllowRedirect($options)
        ->setOptionSsl($options);
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
      case Methods::HEAD:
        $options[CURLOPT_NOBODY] = TRUE;
        break;
      default:
        $options[CURLOPT_CUSTOMREQUEST] = $this->method;
        $this->setOptionData($options);
    }
    return $this;
  }
  
  protected function setOptionProxy(&$options) {
    if ($this->proxy !== FALSE) {
      $options[CURLOPT_PROXY] = $this->proxy['url'];
      if (isset($this->proxy['auth'])) {
        $options[CURLOPT_PROXYAUTH] = $this->proxy['auth'];
      }
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
    return $this;
  }
  
  protected function setOptionSsl(&$options) {
    $options[CURLOPT_SSL_VERIFYPEER] = false;
    if ($this->ssl_ca !== '') {
      $options[CURLOPT_SSL_VERIFYPEER] = true;
      $options[CURLOPT_SSL_VERIFYHOST] =  2;
      $options[CURLOPT_CAINFO] = $this->ssl_ca;
      
    }
    return $this;
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
      return new PHRequestsResolveHostException($message);
    }
    
    if ($errorNro === 28) {
      return new PHRequestsTimeoutException($message);
    }
    
    return new PHRequestsException($message);
  }
}
