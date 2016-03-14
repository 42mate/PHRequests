<?php

namespace PHRequests\Models;

/**
 * Response holds the data of the Response
 *
 * @author agustin
 */
class Response {
  
  /**
   * Creates a Response Object
   * @param Array $response : Response data of Requester
   */
  public function __construct($response) {
    foreach($response as $key => $value) {
      $low_key = strtolower($key);
      if (is_array($value)) {
          $low_values = array();
          foreach ($value as $key => $val) {
              $low_values[strtolower($key)] = strtolower($val);
          }
      } else {
          $low_values = $value;
      }
      $this->$low_key = $low_values;
    }
  }

  /**
   * Returns the Content of the PHRequests
   * @return String
   */
  public function __toString() { 
    if (empty($this->content)) {
      return '';
    }
    return $this->content;
  }
}
