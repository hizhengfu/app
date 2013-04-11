<?php

/**
 * Kirby App Bootstrapper
 */

// direct access protection
if(!defined('KIRBY')) define('KIRBY', true);

// store the directory separator in something simpler to use
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// store the main panel root
if(!defined('ROOT_KIRBY_APP'))     define('ROOT_KIRBY_APP',     dirname(__FILE__));
if(!defined('ROOT_KIRBY_APP_LIB')) define('ROOT_KIRBY_APP_LIB', ROOT_KIRBY_APP . DS . 'lib');
if(!defined('ROOT_KIRBY_TOOLKIT')) define('ROOT_KIRBY_TOOLKIT', ROOT_KIRBY_APP . DS . 'toolkit');

// relative stuff
if(!defined('ROOT_KIRBY_APP_MODULES')) define('ROOT_KIRBY_APP_MODULES', ROOT_KIRBY_APP . DS . 'modules');

// define the main app class
if(!defined('KIRBY_APP_CLASS')) define('KIRBY_APP_CLASS', 'App');

// load the toolkit
include(ROOT_KIRBY_TOOLKIT . DS . 'bootstrap.php');

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
