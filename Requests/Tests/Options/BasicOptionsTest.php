<?php

include_once 'bootstrap.php';

class BasicOptionsTest extends PHPUnit_Framework_TestCase {

  public function testOptionsHead() {
    $response = \Requests\Requests::options(BASE_GET_URL);
    $this->assertEquals($response->http_code, 200);
    $this->assertEquals($response->headers['Allow'], 'HEAD, OPTIONS, GET');
    $this->assertEquals($response->allow, explode(', ', 'HEAD, OPTIONS, GET'));
  }

}
