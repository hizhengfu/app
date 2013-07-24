<?php

/**
 * Kirby App Bootstrapper
 * 
 * Include this file to load all essential 
 * files to initiate a new Kirby App
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

// check for an existing toolkit
if(!defined('KIRBY_TOOLKIT_ROOT')) die('The Kirby Toolkit is required for the Kirby App Framework');

/**
 * Overwritable constants
 * Define them before including the bootstrapper
 * to change essential roots
 */

if(!defined('KIRBY_APP_ROOT'))     define('KIRBY_APP_ROOT',     __DIR__);
if(!defined('KIRBY_APP_ROOT_LIB')) define('KIRBY_APP_ROOT_LIB', KIRBY_APP_ROOT . DS . 'lib');

// initialize the autoloader
$autoloader = new Kirby\Toolkit\Autoloader();

// set the base root where all classes are located
$autoloader->root = KIRBY_APP_ROOT_LIB;

// set the global namespace for all classes
$autoloader->namespace = 'Kirby\\App';

// add all needed aliases
$autoloader->aliases = array(  
  'app'         => 'Kirby\\App',
  'assets'      => 'Kirby\\App\\Assets',
  'controller'  => 'Kirby\\App\\Controller',
  'layout'      => 'Kirby\\App\\Layout',
  'module'      => 'Kirby\\App\\Module',
  'snippet'     => 'Kirby\\App\\Snippet',
  'view'        => 'Kirby\\App\\View',
);

// start autoloading
$autoloader->start();

// load the app class
require_once(KIRBY_APP_ROOT . DS . 'app.php');

// load the default config values
require_once(KIRBY_APP_ROOT . DS . 'defaults.php');

// catch all errors and throw an exception so it can get caught by the app's error event
set_error_handler(function($errno, $errstr, $errfile, $errline) {
  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);  
});

// catch all exceptions with the app's error event
set_exception_handler(function($exception) {
  app::trigger('error', $exception);
});