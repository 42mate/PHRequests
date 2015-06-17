<?php
/**
 * Boostrap to make Tests
 */

function psr0_autoload($className)
{
  $className = ltrim($className, '\\');
  $fileName  = '';
  $namespace = '';
  if ($lastNsPos = strripos($className, '\\')) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
  require './../src/' . $fileName;
}

spl_autoload_register('psr0_autoload');

define('BASE_GET_URL', 'http://httpbin.org/');
define('BASE_GET_URL_HTTPS', 'https://www.google.com.ar');
define('CA_PATH', dirname(__FILE__) . '/Resources/google2.pem');
