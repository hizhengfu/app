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
 * Helper constants
 */

if(!defined('KIRBY'))     define('KIRBY',     true);
if(!defined('DS'))        define('DS',        DIRECTORY_SEPARATOR);
if(!defined('MB_STRING')) define('MB_STRING', (int)function_exists('mb_get_info'));

/**
 * Overwritable constants
 * Define them before including the bootstrapper
 * to change essential roots
 */

if(!defined('KIRBY_APP_ROOT'))     define('KIRBY_APP_ROOT',     dirname(__FILE__));
if(!defined('KIRBY_APP_ROOT_LIB')) define('KIRBY_APP_ROOT_LIB', KIRBY_APP_ROOT . DS . 'lib');

// set the location of your app modules with this constant
if(!defined('KIRBY_APP_ROOT_MODULES')) define('KIRBY_APP_ROOT_MODULES', '');

// define the main app class
if(!defined('KIRBY_APP_CLASS')) define('KIRBY_APP_CLASS', 'App');

// initialize the autoloader
$autoloader = new Kirby\Toolkit\Autoloader();

// set the base root where all classes are located
$autoloader->root = KIRBY_APP_ROOT_LIB;

// set the global namespace for all classes
$autoloader->namespace = 'Kirby\\App';

// add all needed aliases
$autoloader->aliases = array(
  'app'         => 'Kirby\\App\\App',
  'assets'      => 'Kirby\\App\\Assets',
  'controller'  => 'Kirby\\App\\Controller',
  'controllers' => 'Kirby\\App\\Controllers',
  'layout'      => 'Kirby\\App\\Layout',
  'module'      => 'Kirby\\App\\Module',
  'modules'     => 'Kirby\\App\\Modules',
  'response'    => 'Kirby\\App\\Response',
  'view'        => 'Kirby\\App\\View',
);

// start autoloading
$autoloader->start();

// load the default config values
require_once(KIRBY_APP_ROOT . DS . 'defaults.php');

// load the helper functions
require_once(KIRBY_APP_ROOT . DS . 'helpers.php');
