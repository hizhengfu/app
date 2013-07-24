<?php

namespace Kirby;

use Kirby\Toolkit\A;
use Kirby\Toolkit\C;
use Kirby\Toolkit\Dir;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Response;
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
class App {

  // all registered app events
  static public $events = array();

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
 
  /**
   * Returns an array with all modules
   * 
   * @return array
   */
  static public function modules($dir = null) {

    if(!is_null(static::$modules)) return static::$modules;
    if(is_null($dir)) raise('Module installation failed');

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
    if(!static::$route) raise('Page not found', 404);

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
      if(!$module) raise('Invalid module: ' . $m);

      // initial config event for the module
      $module->config();

      // load the controller file
      f::load($module->root() . DS . 'controllers' . DS . $c . '.php');

      // create the controller class name
      $class = $c . 'controller';

      // check if the controller exists
      if(!class_exists($class)) raise('Invalid controller: ' . $c);

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
          
    // run app configuration
    app::configure();

    // call the dispatcher
    $response = static::dispatch();

    if(is_a($response, 'Kirby\\Toolkit\\Response')) {
      $response->header();
    } 
    
    echo $response;

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

  /**
   * Registers a new app event
   * 
   * @param string $event The name of the event
   * @param closure $callback The callback function
   */
  static public function on($event, $callback) {
    static::$events[$event] = $callback;
  }

  /**
   * Triggers a registered event if it exists
   * 
   * @param string $event The name of the event
   * @param array $arguments Optional arguments which should be passed to the event
   * @return mixed
   */
  static public function trigger($event, $arguments = array()) {
    if(!isset(static::$events[$event]) or !is_callable(static::$events[$event])) return false;
    if(!is_array($arguments)) $arguments = array($arguments);
    return call_user_func_array(static::$events[$event], $arguments);
  }

  /**
   * Returns all registered events
   * 
   * @return array
   */
  static public function events() {
    return static::$events;
  }

}