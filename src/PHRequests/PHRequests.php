<?php

namespace PHRequests;

use PHRequests\Models\Methods;
use PHRequests\Models\Request;

class PHRequests {

  /**
   * Makes a PHRequests
   *
   * @param String $method : Some allowed HTTP Method (GET, PUT, POST, etc).
   * @param String $url : The Url to make the request
   * @param Array $options : Options for the PHRequests
   *
   * @return PHRequests\Model\Response
   */
  static public function request($method, $url, $options = array()) {
    $r = new Request($options);

    $data = isset($options['data']) ? $options['data'] : NULL;
    $params = isset($options['params']) ? $options['params'] : NULL;
    $request_headers = isset($options['headers']) ? $options['headers'] : array();

    return $r->execute($method, $url, $data, $params, $request_headers);
  }

  static public function get($url, $options = array()) {
    return self::request(Methods::GET, $url, $options);
  }

  static public function post($url, $options = array()) {
    return self::request(Methods::POST, $url, $options);
  }

  static public function put($url, $options = array()) {
    return self::request(Methods::PUT, $url, $options);
  }

  static public function delete($url, $options = array()) {
    return self::request(Methods::DELETE, $url, $options);
  }

  static public function head($url, $options = array()) {
    return self::request(Methods::HEAD, $url, $options);
  }

  static public function options($url, $options = array()) {
    return self::request(Methods::OPTIONS, $url, $options);
  }

  /**
   * Gets a remote file and save it locally.
   *
   * @param String $url : Url to get the file
   * @param String $localPath : Full path to store the file
   * @param Array $options : PHRequests Options
   * @return boolean true on success.
   * @throws PHRequestException If the File can't be saved.
   */
  static public function saveRemoteFile($url, $localPath, $options) {
    $r = new Request($options);
    return $r->save($localPath, $url);
  }

}
