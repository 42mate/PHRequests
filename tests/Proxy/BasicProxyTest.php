<?php

include_once 'bootstrap.php';

class BasicProxyTest extends PHPUnit_Framework_TestCase {

  public function testProxyHead() {
    //Look here for proxies http://proxylist.hidemyass.com/
    $options = array(
        'proxy' => array(
            'url' => '200.43.219.118:8080'
        ),
    );
    //Disabled since The ips changes always, needs a trustable proxy to test.
    //$response = \PHRequests\PHRequests::options(BASE_GET_URL, $options);
    //$this->assertEquals($response->http_code, 200);
  }

}
