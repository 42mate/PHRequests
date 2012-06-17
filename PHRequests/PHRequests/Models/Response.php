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
   * @param Array $response : Response data of Requester
   */
  public function __construct($response) {
    foreach($response as $key => $value) {
      $this->$key = $value;
    }
  }

  /**
   * Returns the Content of the PHRequests
   * @return String
   */
  public function __toString() {
    return $this->content;
  }
}
