<?php

include_once 'bootstrap.php';

class BasicRequestTest extends PHPUnit_Framework_TestCase {
  
  /**
   * @expectedException Requests\Exceptions\RequestException 
   */
  public function testInvalidMethod() {
    \Requests\Requests::request('INVALID_METHOD', BASE_GET_URL . 'get');
  }

}
