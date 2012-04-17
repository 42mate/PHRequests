<?php

include_once 'bootstrap.php';

class BasicRequestTest extends PHPUnit_Framework_TestCase {
  
  /**
   * @expectedException PHRequests\Exceptions\PHRequestsException 
   */
  public function testInvalidMethod() {
    \PHRequests\PHRequests::request('INVALID_METHOD', BASE_GET_URL . 'get');
  }

}
