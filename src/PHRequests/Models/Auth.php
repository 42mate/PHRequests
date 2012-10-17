<?php

class Auth {
  
  const BASIC = 'BASIC';
  const NTLM = 'NTLM';

  /**
   * Returns an Array with al Defined Auth Methods
   * @return Array
   */
  static public function getMethods() {
    $r = new \ReflectionClass('\PHRequests\Models\Auth');
    return $r->getConstants();
  }
}