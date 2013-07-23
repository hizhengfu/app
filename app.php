<?php

namespace Kirby;

use Exception;
use Kirby\Toolkit\A;
use Kirby\Toolkit\C;
use Kirby\Toolkit\Dir;
use Kirby\Toolkit\Error;
use Kirby\Toolkit\Event;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Router;
use Kirby\Toolkit\Router\Route;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\URI;
use Kirby\Toolkit\URL;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * App
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class App extends Event {

  // module instance cache
  static protected $modules;
    
  // the current active module
  static protected $module;
  
  // the current active controller
  static protected $controller;

  // the current active action
  static protected $action;

  // the current uri object
  static protected $uri;
  
  // all registered routes
  static protected $routes;
  
  // the current route object
  static protected $route;
 
  // the current app error
  static protected $error;

  /**
   * Returns an array with all modules
   * 
   * @return array
   */
  static public function modules($dir = null) {

    if(!is_null(static::$modules)) return static::$modules;
    if(is_null($dir)) app::raise('module-installation', 'Module installation failed', 400);

    static::$modules = array();

    $modules = dir::read($dir);

    foreach($modules as $module) {
      if(!is_dir($dir . DS . $module)) continue;

      $file  = $dir . DS . $module . DS . $module . '.php';
      $class = $module . 'module';

      f::load($file);

      // skip broken modules
      if(!class_exists($class)) continue;

      // add the new module class
      static::$modules[$module] = new $class($file);

    }

    // load all installed modules
    foreach(static::$modules as $module) {
      $module->load();
      $module->autoloader();
    }

    return static::$modules;

  }

  /**
   * Returns a module class by its name or the current module
   * 
   * @return object
   */
  static public function module($name = null) {

    // if no name is specified, return the current module
    if(is_null($name)) return static::$module;

    // return a specific module
    return a::get(static::$modules, $name, null);

  }

  /**
   * Returns the currently active controller
   * 
   * @return object
   */
  static public function controller() {
    return static::$controller;
  }

  /**
   * Returns the currently active action
   * 
   * @return string
   */
  static public function action() {
    return static::$action;
  }

  /**
   * Registers and returns all routes
   *
   * @return array
   */
  static public function routes() {

    if(!is_null(static::$routes)) return static::$routes;

    foreach(static::modules() as $module) {
      // register all routes for each module
      $module->routes();
    }

    return static::$routes = router::routes();

  }

  /**
   * Returns the current route object
   * 
   * @return object
   */
  static public function route() {
    return static::$route;
  }

  /**
   * Initiates and returns the app's current uri
   * 
   * @return object
   */
  static public function uri() {
    if(!is_null(static::$uri)) return static::$uri;
    return static::$uri = new URI(array(
      'subfolder' => c::get('app.subfolder', '@auto'),
      'strip'     => 'index.php'
    ));
  }

  /**
   * Smart url builder
   * You can pass the following:
   * 
   * 1. nothing or / to get the url of the home page
   * 2. a simple uri like some/uri to get the full url
   * 3. a controller path like todos > todos::index to get the url from the router
   * 
   * Add arguments to replace placeholders in the url with them.  
   * 
   * This can be used with the url::to(), url(), and u() shortcuts
   *
   * @param string $path
   * @param array $arguments
   * @return string
   */
  static public function url($path = null, $arguments = array()) {
  
    $baseurl = static::uri()->baseurl();

    // return the home url
    if(is_null($path) or $path == '/') return $baseurl;

    // controller 
    if(str::contains($path, '>')) {

      $route = route::findByAction($path);

      // return the home url if nothing could be found
      if(empty($route)) return static::url();        

      // return the final url
      $url = rtrim($baseurl . '/' . $route->pattern , '/');      

    } else {
      $url = $baseurl . '/' . ltrim($path, '/');
    }

    // replace all placeholders in the url with proper arguments
    if(!empty($arguments)) {
      
      // replace all placeholders with %s so it can be used in sprintf later
      $url = preg_replace('!(\(\:.*?\))!', '%s', $url);
      
      // only use the array values from the arguments array
      $arguments = array_values((array)$arguments);

      // add the pattern as first argument
      array_unshift($arguments, $url);

      // replace all placeholders with arguments
      $url = call_user_func_array('sprintf', $arguments);
    }

    return $url;

  }

  /**
   * Returns the current app error object if set
   * 
   * @return object
   */
  static public function error() {
    return static::$error;
  }

  /**
   * Raises a new app error
   * 
   * @param string $key
   * @param string $message
   * @param int $code
   */
  static public function raise($key, $message, $code = null) {
    $error = error::raise($key, $message, $data = array(), $code);
    static::trigger('error', $error);
    exit();
  }

  /**
   * Fetches all routes, tries to resolve the current uri 
   * loads the current module and controller and fires the 
   * current controller action. 
   *
   * @return mixed
   */
  static public function dispatch() {

    app::trigger('dispatch:before');

    // get the current route
    static::$route = router::run(static::uri()->path());

    // check for an existing route and send to the 404 page if no route exists
    if(!static::$route) return static::raise('page-not-found', 'Page not found', 404);

    // get the router action
    $action = static::$route->action();

    // check for a route closure 
    if(static::$route->isCallable()) {
      
      // get the result of the router call
      $result = static::$route->call();

      // and pass it to the dispatch:after event
      app::trigger('dispatch:after', array(&$result));
      
      // return the result afterwards
      return $result;

    } else {

      // module > controller::action 

      // parse the string and extract all important parts
      preg_match('!^(.*?)\>(.*?)\:\:(.*?)$!i', trim($action), $matches);

      $m = trim($matches[1]); // module
      $c = trim($matches[2]); // controller
      $a = trim($matches[3]); // action

      $module = static::module($m);

      // check if the module is available
      if(!$module) return static::raise('invalid-module', 'Invalid module: ' . $m, 400);

      // initial config event for the module
      $module->config();

      // load the controller file
      f::load($module->root() . DS . 'controllers' . DS . $c . '.php');

      // create the controller class name
      $class = $c . 'controller';

      // check if the controller exists
      if(!class_exists($class)) return static::raise('invalid-controller', 'Invalid controller: ' . $c, 400);

      $controller = new $class;
      $controller->module    = $module;
      $controller->action    = $a;
      $controller->arguments = static::$route->arguments();

      static::$module     = $module;
      static::$controller = $controller;
      static::$action     = $a;

      // run the controller action and return the result
      $result = $controller->run();

      // and pass it to the dispatch:after event
      app::trigger('dispatch:after', array(&$result));

      // return the result afterwards
      return $result;

    }

  }

  /**
   * App configuration
   */
  static public function configure() {
    
    // register all routes
    static::routes();

    // default url handling
    url::$home = app::uri()->baseurl();
    url::$to   = function() {
      return call_user_func_array(array('app', 'url'), func_get_args());
    };

    // let users overwrite/add their own configuration with an event
    app::trigger('configure');

  }

  /**
   * Runs the dispatcher and echos the response
   */
  static public function run() {
    
    try {
      
      // run app configuration
      app::configure();

      // call the dispatcher
      $response = static::dispatch();

      if(is_a($response, 'Kirby\\App\\Response')) {
        $response->header();
      } 
      
      echo $response;
    } catch(Exception $e) {
      static::raise('dispatch-error', $e->getMessage());
    }
  }

  /**
   * Returns the full root for a smart path (tm)
   * 
   * @param string $path 
   * @return string
   */
  static public function root($path) {

    $parts  = str::split($path, '>');
    $module = $parts[0]; 
    $module = static::module($module);

    if(!$module) return false;

    return $module->root() . DS . $parts[1];

  }

  /**
   * A simple loader for collections, models and lib stuff
   * from any module
   * 
   * @param string $path
   */
  static public function load($path) {

    if(is_array($path)) {
      foreach($path as $p) static::load($p);
      return; 
    }

    f::load(static::root($path . '.php'));

  }

}