<?php

define('DS', DIRECTORY_SEPARATOR);

$dir = realpath(dirname(__FILE__));

if(!defined('TEST_ROOT'))     define('TEST_ROOT',     dirname($dir));
if(!defined('TEST_ROOT_ETC')) define('TEST_ROOT_ETC', TEST_ROOT . DS . 'etc');
if(!defined('TEST_ROOT_LIB')) define('TEST_ROOT_LIB', $dir);

// define where the modules are located
define('KIRBY_APP_ROOT_MODULES', TEST_ROOT_ETC . DS . 'modules');

// include the kirby toolkit bootstrapper file
require_once(dirname(dirname(TEST_ROOT)) . DIRECTORY_SEPARATOR . 'toolkit' . DIRECTORY_SEPARATOR . 'bootstrap.php');

// include the kirby app bootstrapper file
require_once(dirname(TEST_ROOT) . DIRECTORY_SEPARATOR . 'bootstrap.php');

// init 
app(array(
  'app.subfolder'  => 'mysubfolder',
  'app.url'        => 'http://superurl.com',
  'app.currentURL' => 'http://superurl.com/mysubfolder/current',
));
