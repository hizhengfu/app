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

if(!defined('KIRBY_APP_ROOT'))         define('KIRBY_APP_ROOT',         dirname(__FILE__));
if(!defined('KIRBY_APP_ROOT_LIB'))     define('KIRBY_APP_ROOT_LIB',     KIRBY_APP_ROOT . DS . 'lib');
if(!defined('KIRBY_APP_ROOT_TOOLKIT')) define('KIRBY_APP_ROOT_TOOLKIT', KIRBY_APP_ROOT . DS . 'toolkit');

// set the location of your app modules with this constant
if(!defined('KIRBY_APP_ROOT_MODULES')) define('KIRBY_APP_ROOT_MODULES', '');

// define the main app class
if(!defined('KIRBY_APP_CLASS')) define('KIRBY_APP_CLASS', 'App');

// load the toolkit
include(KIRBY_APP_ROOT_TOOLKIT . DS . 'bootstrap.php');

/**
 * Loads all missing app classes on demand
 * 
 * @param string $class The name of the missing class
 * @return void
 */
function appLoader($class) {
  f::load(KIRBY_APP_ROOT_LIB . DS . strtolower($class) . '.php');
}

// register the autoloader function
spl_autoload_register('appLoader');

// load the default config values
require_once(KIRBY_APP_ROOT . DS . 'defaults.php');

// load the helper functions
require_once(KIRBY_APP_ROOT . DS . 'helpers.php');
