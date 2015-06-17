<?php

include_once 'bootstrap.php';

class BasicOptionsTest extends PHPUnit_Framework_TestCase {

  public function testOptionsHead() {
    $response = \PHRequests\PHRequests::options(BASE_GET_URL);
    //var_dump($response); die();
    $this->assertEquals($response->http_code, 200);
    $this->assertEquals(strtoupper($response->headers['allow']), 'HEAD, OPTIONS, GET');
    $this->assertEquals(strtoupper($response->allow[0]), 'HEAD');
    $this->assertEquals(strtoupper($response->allow[2]), 'GET');
  }

}
