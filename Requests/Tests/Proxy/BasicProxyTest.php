<?php

include_once 'bootstrap.php';

class BasicProxyTest extends PHPUnit_Framework_TestCase {

  public function testProxyHead() {
    $options = array(
        'proxy' => array(
            'url' => '201.234.220.99:3128'         
        ),
    );
    $response = \Requests\Requests::options(BASE_GET_URL, $options);
    $this->assertEquals($response->http_code, 200);
  }

}
