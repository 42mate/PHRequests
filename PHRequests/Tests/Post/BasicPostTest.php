<?php

include_once 'bootstrap.php';

class BasicPostTest extends PHPUnit_Framework_TestCase {

  public function testBasicPost() {
    $options = array(
        'data' => array(
            'po1' => 'faa',
        ),
    );
    
    $response = \PHRequests\PHRequests::post(BASE_GET_URL . 'post', $options);
    $this->assertEquals($response->http_code, 200);
    
    $response = \PHRequests\PHRequests::post(BASE_GET_URL . 'noneError');  
    $this->assertEquals($response->http_code, 400);
  }

  public function testParameterPost() {
    
    $options = array(
        'params' => array(
            'var1' => 1,
            'var2' => 'Hello',
        ),
        'data' => array(
            'po1' => 11,
            'po2' => 'Hello Post',
        )
    );
    
    $response = \PHRequests\PHRequests::post(BASE_GET_URL . 'post', $options);
    $this->assertEquals($response->http_code, 200); 
    $jres = json_decode($response->content);
    $this->assertEquals(isset($jres->args), TRUE);    
    $this->assertEquals(isset($jres->args->var1), TRUE);
    $this->assertEquals(isset($jres->args->var2), TRUE); 
    $this->assertEquals(isset($jres->form->po1), TRUE);
    $this->assertEquals(isset($jres->form->po2), TRUE); 
    $this->assertEquals($jres->args->var1, 1);
    $this->assertEquals($jres->args->var2, 'Hello');
    $this->assertEquals($jres->form->po1, 11);
    $this->assertEquals($jres->form->po2, 'Hello Post');
    $this->assertEquals((string) $response, $response->content);
  }
  
}
