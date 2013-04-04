<?php

/**
 * Kirby App Bootstrapper
 */

// direct access protection
if(!defined('KIRBY')) define('KIRBY', true);

// store the directory separator in something simpler to use
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// store the main toolkit root
if(!defined('ROOT_KIRBY_APP'))     define('ROOT_KIRBY_APP',     dirname(__FILE__));
if(!defined('ROOT_KIRBY_APP_LIB')) define('ROOT_KIRBY_APP_LIB', ROOT_KIRBY_APP . DS . 'lib');

/**
 * Loads all missing app classes on demand
 * 
 * @param string $class The name of the missing class
 * @return void
 */
function appLoader($class) {

  $file = ROOT_KIRBY_APP_LIB . DS . r($class == 'App', 'app', strtolower(str_replace('App', '', $class))) . '.php';

  if(file_exists($file)) {
    require_once($file);
    return;
  } 

}

// register the autoloader function
spl_autoload_register('appLoader');

// load the default config values
require_once(ROOT_KIRBY_APP . DS . 'defaults.php');

// load the helper functions
require_once(ROOT_KIRBY_APP . DS . 'helpers.php');
