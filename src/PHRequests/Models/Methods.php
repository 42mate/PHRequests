<?php

namespace PHRequests\Models;

/**
 * Provides a Definition for all Allowed Methods
 *
 * @author agustin
 */
class Methods {
  
  const POST = 'POST';
  const GET = 'GET';
  const PUT = 'PUT';
  const HEAD = 'HEAD';
  const DELETE = 'DELETE';
  const OPTIONS = 'OPTIONS';
  
  /**
   * Returns an Array with al Defined Methods
   * @return Array
   */
  static public function getMethods() {
    $r = new \ReflectionClass('\PHRequests\Models\Methods');
    return $r->getConstants();
  }
  
}
