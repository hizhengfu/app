<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * App
 * 
 * The main object, which is used to retrieve all sub objects
 * and to organize some basic functionality
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class App {

  // Cache for the app subfolder
  protected $subfolder = null;
  
  // Cache for the uri object
  protected $uri = null;
    
  // The main url of the app
  protected $url = null;
  
  // http or https
  protected $scheme = null;

  // Cache for the Modules list
  protected $modules = null;
  
  // stores the current module
  protected $module = null;

  // stores the currently used controller
  protected $controller = null;

  // stores the called action
  protected $action = null;

  // stores the route
  protected $route = null;

  /**
   * Constructor
   * 
   * @param array $params An optional array of params to configure the app instance
   */
  public function __construct($params = array()) {

    // start a new session
    s::start();

    // check for stored flash messages
    $this->flash();

    // load all configs 
    $this->configure($params);
    
  }

  /**
   * Placeholder for app classes to create global routes
   */
  public function routes() {
    return true;
  }

  /**
   * Returns the subfolder(s)
   * A subfolder will be auto-detected or can be set in the config file
   * If you run the site under its own domain, the subfolder will be empty
   * 
   * @return string
   */
  public function subfolder() {

    if(!is_null($this->subfolder)) return $this->subfolder;

    // try to detect the subfolder      
    $subfolder = (c::get('app.subfolder') !== false) ? trim(c::get('app.subfolder'), '/') : trim(dirname(server::get('script_name')), '/\\');

    c::set('app.subfolder', $subfolder);

    return $this->subfolder = $subfolder;

  }

  /**
   * Returns the URI object, which can be used
   * to inspect and work with the current URL/URI
   * 
   * @return object Uri
   */
  public function uri($uri = null) {

    if(!is_null($this->uri)) return $this->uri;

    return $this->uri = new Uri(array(
      'subfolder' => $this->subfolder(),
      'url'       => c::get('app.currentURL', null)
    ));

  }

  /**
   * Returns the scheme (http or https)
   *
   * @return string
   */
  public function scheme() {
    if(!is_null($this->scheme)) return $this->scheme;
    return $this->uri()->scheme();
  }

  /**
   * Returns the base url of the site
   * The url is auto-detected by default and can 
   * also be set in the config like the subfolder
   * 
   * @return string
   */
  public function url($uri = null) {

    if(is_null($this->url)) {

      // auto-detect the url if it is not set
      $url = (c::get('app.url') === false) ? $this->scheme() . '://' . $this->uri()->host() : rtrim(c::get('app.url'), '/');

      if($subfolder = $this->subfolder()) {
        // check if the url already contains the subfolder      
        // so it's not included twice
        if(!preg_match('!' . preg_quote($subfolder) . '$!i', $url)) $url .= '/' . $subfolder;      
      }
                    
      c::set('app.url', $url);  
      $this->url = $url;

    }

    // make sure to not convert absolute urls  
    if(preg_match('!^(http|https)!i', $uri)) return $uri;
    
    // make sure to avoid additional slashes
    $uri = trim($uri, '/');

    // module urls
    if(str::contains($uri, '>')) {

      $parts  = str::split($uri, '>');
      $module = $parts[0];
      $path   = $parts[1];

      return $this->url . '/modules/' . $module . '/' . $path;

    }

    // home
    if(empty($uri)) return $this->url;

    return $this->url . '/' . $uri;

  }

  /**
   * Returns the full root for a smart path (tm)
   * 
   * @param string $path 
   * @return string
   */
  public function root($path) {

    $parts      = str::split($path, '>');
    $moduleName = $parts[0]; 
    $module     = $this->modules()->get($moduleName);

    if(!$module) return false;

    return $module->root() . DS . $parts[1];

  }

  /**
   * Returns a list with all available modules
   * 
   * @return object Modules
   */
  public function modules() {
    if(!is_null($this->modules)) return $this->modules;
    return $this->modules = new Modules();
  }

  /**
   * Returns the current module
   */
  public function module() {
    return $this->module;
  }

  /**
   * Returns the current controller
   */
  public function controller() {
    return $this->controller;
  }

  /**
   * Returns the current route
   */
  public function action() {
    return $this->action;
  }

  /**
   * Returns the current route
   */
  public function route() {
    return $this->route;
  }

  /**
   * Checks and sets the flash message
   * 
   * @param string $type a type for the flash message if you want to use this as setter. This makes it possible to store different flash messages for different types of stuff (error, notice, etc.)
   * @param string $message The message which should be stored
   * @return mixed If no type is specified this will return the last message
   */
  public function flash($type = false, $message = false) {

    $flash = s::get('flash', array());

    // check script
    if(!$type) {
      foreach($flash as $type => $params) {    
        if(!$params['viewed']) {
          $flash[$type]['viewed'] = true;
        } else {
          unset($flash[$type]);
        }
      }    
      return s::set('flash', $flash);
    }
    
    // getter
    if(!$message) {
      return @$flash[$type]['message'];
    }

    $flash[$type] = array(
      'message' => $message,
      'viewed'  => false
    );

    s::set('flash', $flash);

  }

  /**
   * Checks / returns a csfr token
   * 
   * @param string $check Pass a token here to compare it to the one in the session
   * @return mixed Either the token or a boolean check result
   */
  public function csfr($check = null) {
    
    if(is_null($check)) {
      $token = str::random(64);
      s::set('csfr', $token);
      return $token;
    }

    return ($check === s::get('csfr')) ? true : false;

  }

  /**
   * Match the url to a module, controller and action
   * 
   * @return string The controller response
   */
  public function dispatch() {

    // register all app routes
    $this->routes();

    // register all module routes
    foreach($this->modules() as $module) {
      $module->routes();      
    }

    // find the currently active route
    $route = router::match($this->uri()->path());

    // react on missing routes
    if(!$route) raise('Not found: ' . $this->uri());

    // store the used route
    $this->route = $route;

    $action     = $route->action();
    $parts      = str::split($action, '>');
    $moduleName = $parts[0]; 
    $module     = $this->modules()->get($moduleName);

    if(!$module) raise('Invalid module: ' . $moduleName);

    // store the current module
    $this->module = $module;

    $actionParts    = str::split($parts[1], '::');
    $controllerName = $actionParts[0];
    $actionName     = $actionParts[1];      
    $controller     = $module->controllers()->get($controllerName);

    if(!$controller) raise('Invalid controller: ' . $controllerName);

    // store the current controller and action
    $this->controller = $controller;
    $this->action     = $actionName;

  }

  /**
   * Renders the app html
   */
  public function show() {

    // get the current user
    $this->user();

    // find the current controller
    $this->dispatch();

    // authenticate 
    $this->authenticate();

    // apply localization and translation settings
    $this->localize();

    // call the controller action
    echo $this->controller()->call($this->action(), $this->route()->options());

  }

  // protected methods

  /**
   * Loads all config files 
   * 
   * @param array $params An optional array of params, which should be merged
   */
  protected function configure($params = array()) {
    c::set($params);
  }

  /**
   * Initializes some basic local settings
   */  
  protected function localize() {

    // set the timezone to make sure we 
    // avoid errors in php 5.3
    @date_default_timezone_set(c::get('app.timezone', 'UTC'));

  } 

  /**
   * Dummy authentication method
   * Should be overwritten by the app
   */
  protected function authenticate() {
    return false;
  }

  /**
   * A simple loader for collections, models and lib stuff
   * from any module
   * 
   * @param string $path
   */
  public static function load($path) {

    if(is_array($path)) {
      foreach($path as $p) self::load($p);
      return; 
    }

    f::load(app()->root($path . '.php'));

  }

}