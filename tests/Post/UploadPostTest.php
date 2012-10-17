<?php

include_once 'bootstrap.php';

class UploadPostTest extends PHPUnit_Framework_TestCase {

  public function testUploadPost() {
    $path = dirname(__FILE__);
    $options = array(
        'data' => array(
            'po1' => 'faa',
            'file1' => "@$path/../Resources/fake.php",
            'file2' => "@$path/../Resources/fake.php",
        ),
    );    
    $response = \PHRequests\PHRequests::post(BASE_GET_URL . 'post', $options);
    $this->assertEquals($response->http_code, 200);
    $jres = json_decode($response->content);    
    $this->assertTrue(isset($jres->files));
    $this->assertTrue(isset($jres->files->file1));
    $this->assertTrue(isset($jres->files->file2)); 
    $this->assertEquals($jres->files->file1, file_get_contents("$path/../Resources/fake.php"));
  }
  
}
