<?php

include_once 'bootstrap.php';

class BasicDeleteTest extends PHPUnit_Framework_TestCase {

  public function testBasicDelete() {
    $options = array(
        'data' => array(
         'po1' => 'faa',
        ),
    );
    
    $response = \PHRequests\PHRequests::delete(BASE_GET_URL . 'delete', $options);
    $this->assertEquals($response->http_code, 200);
    
    $response = \PHRequests\PHRequests::delete(BASE_GET_URL . 'noneError');   
    $this->assertEquals($response->http_code, 404);
  }

  public function testParameterDelete() {
    
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
    
    $response = \PHRequests\PHRequests::delete(BASE_GET_URL . 'delete', $options);
    $this->assertEquals($response->http_code, 200); 
    $jres = json_decode($response->content);
    $this->assertEquals(isset($jres->args), TRUE);    
    $this->assertEquals(isset($jres->args->var1), TRUE);
    $this->assertEquals(isset($jres->args->var2), TRUE); 
    $this->assertEquals(isset($jres->data), TRUE);
    $this->assertEquals($jres->args->var1, 1);
    $this->assertEquals($jres->args->var2, 'Hello');
    $this->assertEquals($jres->data, 'po1=11&po2=Hello+Post');
    $this->assertEquals((string) $response, $response->content);
  }
  
}
