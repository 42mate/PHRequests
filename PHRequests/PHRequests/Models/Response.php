<?php

namespace PHRequests\Models;

/**
 * Response holds the data of the Reponse
 *
 * @author agustin
 */
class Response {
  
  /**
   * Creates a Response Object
   * @param String $content  : Raw Response (Headers and Body)
   * @param Array $info : Curl Meta Info    
   */
  public function __construct($content, $info = array()) {
    foreach($info as $key => $value) {
      $this->$key = $value;
    }
    $this->status_code = $this->http_code;
    $this->raw_header = trim(substr($content, 0, $info['header_size']));
    $this->headers = $this->parseHttpHeader($this->raw_header);
    $this->content = substr($content, -$info['download_content_length']); 
    $this->allow = array();
    if (isset($this->headers['Allow'])) {
      $this->allow = explode(', ', $this->headers['Allow']);
    }
  }
  
  /**
   * Parses a raw HTTP Response Header and convert it to an array.
   * @param String $headers
   * @return Array : The Parsed Headers 
   */
  protected function parseHttpHeader($headers) {
    $headerdata = array();
    if ($headers === false){
      return $headerdata;
    }
    $headers_lines = str_replace("\r","", $headers);
    $headers_lines = explode("\n", $headers_lines);
    foreach($headers_lines as $value){
      $header = explode(": ", $value);
      if(count($header) == 1){
        $headerdata['status'] = $header[0];
      } elseif(count($header) == 2){
        $headerdata[$header[0]] = $header[1];
      }
    }
    return $headerdata;
  }

  /**
   * Returns the Content of the PHRequests
   * @return String
   */
  public function __toString() {
    return $this->content;
  }
}
