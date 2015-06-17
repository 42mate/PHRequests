<?php
namespace PHRequests\Models;

/**
 * Class for interact with Http Requests.
 *
 * @author Casiva Agustin
 */
class Requester {

  const POST = 'POST';
  const GET = 'GET';
  const HEAD = 'HEAD';
  const DELETE = 'DELETE';
  const PUT = 'PUT';
  const PROXY_AUTH_NTLM = 'NTLM';
  const PROXY_AUTH_BASIC = 'BASIC';
  const AUTH_BASIC = CURLAUTH_BASIC;
  const AUTH_NTLM = CURLAUTH_NTLM;
  const AUTH_DIGEST = CURLAUTH_DIGEST;
  const AUTH_GSS = CURLAUTH_GSSNEGOTIATE;
  const RESPONSE_RAW = 'raw';
  const RESPONSE_ARRAY = 'array';
  const UA_FIREFOX = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
  const UA_CHROME = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';

  static protected $default_options = array(
    CURLOPT_FRESH_CONNECT => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLINFO_HEADER_OUT => TRUE,
    CURLOPT_SSL_VERIFYPEER => FALSE,
    CURLOPT_SSL_VERIFYHOST => FALSE,
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_MAXREDIRS => 3,
    CURLOPT_FAILONERROR => FALSE,
    CURLOPT_HEADER => FALSE,
    CURLOPT_USERAGENT => self::UA_FIREFOX,
  );

  protected $url = '';
  protected $method = self::GET;
  protected $responseType = self::RESPONSE_RAW;
  protected $options = array();
  protected $lastHttpCode = 0;
  protected $httpRequestHeaders = array();

  /**
   * Creates a Request Object
   *
   * @param $options
   *
   * @see resetOptions for more detail of the options
   */
  public function __construct($options = array()) {
    $this->resetOptions($options);
  }

  /**
   * Executes the Request
   *
   * @param String $method : The HTTP method to use, by default use the internal Method.
   * @param String $url : The url to hit
   * @param Array $data : The Data to append in the body
   * @param Array $params : The Parameters to append as a Query String
   * @param Array $request_headers : Array of strings with Headers directives.
   *
   * @throws \Exception
   * @return String|Array|Boolean : The content or false on failure
   */
  public function execute($method, $url, $data = NULL, $params = NULL, $request_headers = array()) {
    $this->setOptionUrl($url);
    $this->setOptionMethod($method);
    $this->setOptionData($data);
    $this->setOptionParams($params);
    $this->setOptionHttpRequestHeaders($request_headers);

    $ch = curl_init();
    curl_setopt_array($ch, $this->options);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch);

    if (curl_errno($ch) > 0) {
      throw new \Exception(curl_error($ch), curl_errno($ch));
    }

    if ($this->responseType === self::RESPONSE_ARRAY) {
      $result = self::createArrayResponse($result, $info);
    }

    $this->lastHttpCode = $info['http_code'];

    curl_close($ch);
    return $result;
  }

  /**
   * Executes a Get Request
   *
   * @param String $url
   * @param Array|String $params
   *
   * @return String
   *
   * @throw Exception
   */
  public function get($url, $params = NULL) {
    return $this->execute(self::GET, $url, NULL, $params);
  }

  /**
   * Executes a Post Request
   *
   * @param String $url
   * @param Array|String $data : Data to add in the body of the Request
   * @param Array|String $params : Parameters to include at the Url, ?arg1=1&arg2=2....
   *
   * @return String
   *
   * @throw Exception
   */
  public function post($url, $data = NULL, $params = NULL) {
    return $this->execute(self::POST, $url, $data, $params);
  }

  /**
   * Executes a Put Request
   *
   * @param String $url
   * @param Array|String $data : Data to add in the body of the Request
   * @param Array|String $params : Parameters to include at the Url, ?arg1=1&arg2=2....
   *
   * @return String
   *
   * @throw Exception
   */
  public function put($url, $data = NULL, $params = NULL) {
    return $this->execute(self::PUT, $url, $data, $params);
  }

  /**
   * Executes a Delete Request
   *
   * @param String $url
   * @param Array|String $data : Data to add in the body of the Request
   * @param Array|String $params : Parameters to include at the Url, ?arg1=1&arg2=2....
   *
   * @return String
   *
   * @throw Exception
   */
  public function delete($url, $data = NULL, $params = NULL) {
    return $this->execute(self::DELETE, $url, $data, $params);
  }

  /**
   * Executes a Head Request
   *
   * @param String $url
   * @param Array|String $data : Data to add in the body of the Request
   * @param Array|String $params : Parameters to include at the Url, ?arg1=1&arg2=2....
   *
   * @return String
   *
   * @throw Exception
   */
  public function head($url, $data = NULL, $params = NULL) {
    return $this->execute(self::HEAD, $url, $data, $params);
  }

  /**
   * Saves the Request in store path
   *
   * @param String $storePath : Full path to store the file
   * @param String $url : The url to hit
   * @param Array $data : The Data to append in the body
   * @param Array $params : The Parameters to append as a Query String
   * @param String $method : An HTTP Method, by default GET
   * @return boolean          : True on success False on fail
   */
  public function save($storePath, $url, $data = NULL, $params = NULL, $method = self::GET) {
    $fileContent = $this->execute($method, $url, $data, $params);
    $fp = fopen($storePath, 'w');
    if ($fp !== FALSE) {
      $writeStatus = fwrite($fp, $fileContent);
      if ($writeStatus !== FALSE) {
        fclose($fp);
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Pings to the Url to check of works
   *
   * @param  $url : Url to hit
   * @return boolean : True on Success False on Fail
   */
  public function ping($url) {
    try {
      if ($this->execute(self::HEAD, $url) !== FALSE) {
        return TRUE;
      }
    } catch (\Exception $e) {
      return FALSE;
    }
    return FALSE;
  }

  /**
   * Converts an array to a query string
   *
   * @param Array $params
   *
   * @return String
   */
  public function buildQuery($params) {
    if (is_array($params)) {
      return http_build_query($params);
    }
    return $params;
  }

  /**
   * Sets the Proxy Parameters
   *
   * @param Array|bool $proxy : Array with Configurations
   *      array('url',       //Proxy Url
   *            'auth',      //Proxy Auth credentials User:Pass, Optional
   *            'auth_method'//Proxy Auth Method, BASIC / NTLM, Basic By Def
   *      )
   * @return Requester
   */
  public function setOptionProxy($proxy = FALSE) {
    if ($proxy !== FALSE) {
      $this->options[CURLOPT_PROXY] = $proxy['url'];
      if (isset($proxy['auth'])) {
        $this->options[CURLOPT_PROXYAUTH] = CURLAUTH_BASIC;
        if (isset($proxy['auth_method']) && $proxy['auth_method'] === self::PROXY_AUTH_NTLM) {
          $this->options[CURLOPT_PROXYAUTH] = CURLAUTH_NTLM;
        }
        $this->options[CURLOPT_PROXYUSERPWD] = $proxy['auth'];
      }
    }
    return $this;
  }

  /**
   * Sets the Timeout of the Request
   *
   * @param Integer $timeOut , by default 30
   * @return Requester
   */
  public function setOptionTimeOut($timeOut = 30) {
    $this->options[CURLOPT_TIMEOUT] = $timeOut;
    return $this;
  }

  /**
   * Sets how many redirects will support
   *
   * @param Integer $max_redirects , By default 3
   *
   * @return Requester
   */
  public function setOptionAllowRedirect($max_redirects = 3) {
    if ($max_redirects == FALSE || $max_redirects == 0) {
      $this->options[CURLOPT_FOLLOWLOCATION] = FALSE;
      $this->options[CURLOPT_MAXREDIRS] = 0;
      return $this;
    }
    $this->options[CURLOPT_FOLLOWLOCATION] = TRUE;
    $this->options[CURLOPT_MAXREDIRS] = $max_redirects;
    return $this;
  }

  /**
   * Sets the Certificate in order to Validate the Peer
   *
   * @param String $sslCa : Path to the CA Cert
   *
   * @return Requester
   */
  public function setOptionSsl($sslCa) {
    $this->options[CURLOPT_SSL_VERIFYPEER] = FALSE;
    $this->options[CURLOPT_SSL_VERIFYHOST] = FALSE;
    if ($sslCa !== '') {
      $this->options[CURLOPT_SSL_VERIFYPEER] = TRUE;
      $this->options[CURLOPT_SSL_VERIFYHOST] = 2;
      $this->options[CURLOPT_CAINFO] = $sslCa;
    }
    return $this;
  }

  /**
   * Sets auth for the Requests
   *
   * @param String $usernameAndPassword : username:password
   * @param int|String $method : Any Curl Option valid for CURLOPT_HTTPAUTH, by def BASIC.
   *
   * @todo Test this method
   */
  public function setOptionHttpAuth($usernameAndPassword, $method = self::AUTH_BASIC) {
    $this->options[CURLOPT_HTTPAUTH] = $method;
    $this->options[CURLOPT_USERPWD] = $usernameAndPassword;
  }

  /**
   * Sets the Encoding
   *
   * @param string $encoding
   * @internal param String $encodig : "identity", "deflate", and "gzip"
   *
   * @return Requester
   *
   * @todo Test this Method
   */
  protected function setOptionEncoding($encoding = '') {
    $this->options[CURLOPT_ENCODING] = $encoding;
    return $this;
  }

  /**
   * Sets the Url to Hit
   * @param String : $url
   * @return Requester
   */
  protected function setOptionUrl($url) {
    $this->options[CURLOPT_URL] = $url;
    return $this;
  }

  /**
   * Sets the http headers values for the request.
   */
  protected function setOptionHttpRequestHeaders($requestHeaders) {
    $requestHeaders = array_merge($this->httpRequestHeaders, $requestHeaders);
    if (!empty($requestHeaders)) {
      $this->options[CURLOPT_HTTPHEADER] = $requestHeaders;
    }
    $this->httpRequestHeaders = array();
  }

  /**
   * Sets params and Appends to the Url as Query string
   *
   * @param Array $params
   *
   * @return Requester
   */
  protected function setOptionParams($params) {
    if (!empty($params)) {
      $query_params = $this->buildQuery($params);
      $this->options[CURLOPT_URL] .= '?' . $query_params;
    }
    return $this;
  }

  /**
   * Sets Payload for POST requests
   *
   * @param Mixed (Array or String) $data
   * @return Requester
   */
  protected function setOptionData($data) {
    if (!empty($data)) {
      if ($this->method === self::DELETE && is_array($data)) {
        //DELETE needs the post data as string.
        $data = $this->buildQuery($data);
      }
      $this->options[CURLOPT_POSTFIELDS] = $data;
    }
    return $this;
  }

  /**
   * Sets the Request HTTP Method
   *
   * @param String $method : The Method, by default GET
   *
   * @return Requester
   */
  protected function setOptionMethod($method = self::GET) {
    $this->options[CURLOPT_NOBODY] = FALSE;
    $this->method = $method;
    switch ($this->method) {
      case self::GET:
        $this->options[CURLOPT_POST] = FALSE;
        break;
      case self::POST:
        $this->options[CURLOPT_POST] = TRUE;
        break;
      case self::HEAD:
        $this->options[CURLOPT_HEADER] = TRUE;
        $this->options[CURLOPT_NOBODY] = TRUE;
        break;
      default:
        $this->options[CURLOPT_CUSTOMREQUEST] = $this->method;
    }
    return $this;
  }

  /**
   * This options allows set the response type. By default will return a string
   * with the content. But if you need more info, you can set the response to an
   * array and get a complete information of the request.
   *
   * @param String $type RESPONSE_RAW | RESPONSE_ARRAY
   *
   * @return $this
   */
  public function setOptionResponseType($type = self::RESPONSE_RAW) {
    $this->responseType = $type;
    if ($type === self::RESPONSE_ARRAY) {
      $this->options[CURLOPT_HEADER] = TRUE;
    }
    else {
      $this->options[CURLOPT_HEADER] = FALSE;
    }
    return $this;
  }

  /**
   * Set this option to throw an Exception if the response status code is
   * grater or equal than 400. By default is false.
   *
   * @param boolean $fail
   *
   * @return $this
   */
  public function setOptionFailOnError($fail = FALSE) {
    $this->options[CURLOPT_FAILONERROR] = $fail;
    return $this;
  }

  /**
   * Resets Requester Options
   *
   * @param Array $options Possible entries
   *   timeout        : Time in seconds to wait for the request, Default 30
   *   max_redirects  : Numeric, default 3, 0 for don't allow redirects
   *   proxy          : Array (url, auth, auth_method). Default None
   *   encoding       : String, The encoding type to pass to curl, Default ''
   *   ssl_ca         : Sets the Path to the CA for SSL
   *   response_type  : RESPONSE_RAW or RESPONSE_ARRAY
   *                    Sets the response type, the raw response or an array with details.
   *
   * @return $this
   *
   * @todo Test this method
   */
  public function resetOptions($options = array()) {
    $this->options = self::$default_options;
    if (isset($options['timeout']) && is_numeric($options['timeout'])) {
      $this->setOptionTimeOut($options['timeout']);
    }
    if (isset($options['max_redirects']) && is_numeric($options['max_redirects'])) {
      $this->setOptionAllowRedirect((int) $options['max_redirects']);
    }
    if (isset($options['proxy']) && is_array($options['proxy'])) {
      $this->setOptionProxy($options['proxy']);
    }
    if (isset($options['ssl_ca'])) {
      $this->setOptionSsl($options['ssl_ca']);
    }
    if (isset($options['proxy'])) {
      $this->setOptionProxy($options['proxy']);
    }
    if (isset($options['encoding'])) {
      $this->setOptionEncoding($options['encoding']);
    }
    if (isset($options['response_type']) && $options['response_type'] === self::RESPONSE_ARRAY) {
      $this->setOptionResponseType(self::RESPONSE_ARRAY);
    }

    if (isset($options['fail_on_error'])) {
      $this->setOptionFailOnError($options['fail_on_error']);
    }
    return $this;
  }

  /**
   * Parses a raw HTTP Response Header and convert it to an array.
   *
   * @param String $headers
   *
   * @return Array : The Parsed Headers
   */
  static function parseHttpHeader($headers) {
    $headerdata = array();
    if ($headers === FALSE) {
      return $headerdata;
    }
    $headers_lines = str_replace("\r", "", $headers);
    $headers_lines = explode("\n", $headers_lines);
    foreach ($headers_lines as $value) {
      $header = explode(": ", $value);
      if (count($header) == 1) {
        $headerdata['status'] = $header[0];
      }
      elseif (count($header) == 2) {
        $headerdata[strtolower($header[0])] = $header[1];
      }
    }
    return $headerdata;
  }

  /**
   * Creates a Response Array
   *
   * @param String $content : Raw Response (Headers and Body)
   *
   * @param Array $info : Curl Meta Info
   *
   * @return array
   */
  static function createArrayResponse($content, $info = array()) {
    $response = array();
    foreach ($info as $key => $value) {
      $response[$key] = $value;
    }
    $response['raw_header'] = trim(substr($content, 0, $info['header_size']));
    $response['headers'] = self::parseHttpHeader($response['raw_header']);
    $response['content'] = substr($content, $info['header_size']);
    $response['allow'] = array();
    if (isset($response['headers']['allow'])) {
      $response['allow'] = explode(', ', $response['headers']['allow']);
    }
    return $response;
  }

  /**
   * Returns the HTTP code of the last Request.
   *
   * If any request was made before, this method will return 0
   *
   * @return integer
   */
  public function getLastHttpCode() {
    return $this->lastHttpCode;
  }

  /**
   * Change the user agent.
   *
   * See here for more UA signatures http://www.useragentstring.com/pages/useragentstring.php
   *
   * @param $ua
   */
  public function setUserAgent($ua) {
    $this->options[CURLOPT_USERAGENT] = $ua;
  }

  /**
   * Set firefox as user agent
   */
  public function setUserAgentFirefox() {
    $this->setUserAgent(self::UA_FIREFOX);
  }

  /**
   * Set chrome as user agent
   */
  public function setUserAgentChrome() {
    $this->setUserAgent(self::UA_CHROME);
  }

  /**
   * Sets the Request Headers
   *
   * @param string $header_directive
   *
   */
  public function setHttpRequestHeader($header_directive) {
    $this->httpRequestHeaders[] = $header_directive;
  }
}
