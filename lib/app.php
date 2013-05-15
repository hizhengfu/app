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
  
  // Cache for the KirbyAppModules list
  protected $modules = null;
  
  // The main url of the app
  protected $url = null;
  
  // http or https
  protected $scheme = null;
  
  // stores the current module
  protected $module = null;

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
    
    // apply localization and translation settings
    $this->localize();
    
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
   * Returns the KirbyURI object, which can be used
   * to inspect and work with the current URL/URI
   * 
   * @return object KirbyUri
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
  public function url($uri = null, $lang = false) {

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
   * Returns a list with all available modules
   * 
   * @return object KirbyAppModules
   */
  public function modules() {
    if(!is_null($this->modules)) return $this->modules;
    return $this->modules = new AppModules();
  }

  /**
   * Returns the current module
   */
  public function module() {
    if(!is_null($this->module)) return $this->module;
    // find the currently active module
    return $this->module = $this->modules()->findActive();
  }

  /**
   * Returns the default module
   * 
   * @return object KirbyAppModule
   */
  public function defaultModule() {
    return null;
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
   * Embeds css by url
   * 
   * @param string $url the relative or absolute url
   * @param string $media An optional media string
   * @return string
   */
  public function css($url, $media = null) {
    return html::stylesheet($this->url($url), $media);
  }

  /**
   * Embeds js by url
   * 
   * @param string $url the relative or absolute url
   * @param boolean $async Optionally the js tag can include the new async attribute
   * @return string
   */
  public function js($url, $async = false) {
    return html::script($this->url($url), $async);
  }

  /**
   * Includes a snippet from a smart path
   * 
   * @param string $path The smart path to the snippet
   * @param array $data Optional data, which should be passed to the snippet
   * @param boolean $return By default the snippet is echoed, but you can return the result by passing true here. 
   * @return string
   */
  public function snippet($path, $data = array(), $return = false) {

    $parts      = str::split($path, '>');
    $moduleName = $parts[0]; 
    $module     = $this->modules()->get($moduleName);

    if(!$module) return false;

    $file = $module->root() . DS . 'snippets' . DS . $parts[1] . '.php';
    
    return tpl::loadFile($file, $data, $return);

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
   * Renders the app html
   */
  public function show() {

    // run authentication
    $this->authenticate();
    
    // find the current module and run it
    echo $this->module()->controller()->response();

  }

  // protected methods

  /**
   * Loads all config files 
   * 
   * @param array $params An optional array of params, which should be merged
   */
  protected function configure($params = array()) {
    return false;
  }

  /**
   * Initializes some basic local settings
   */  
  protected function localize() {

    // set the timezone to make sure we 
    // avoid errors in php 5.3
    @date_default_timezone_set(c::get('app.timezone', 'UTC'));

    // set default locale settings for php functions
    if(c::get('app.locale')) setlocale(LC_ALL, c::get('app.locale'));

    // load the language: TODO replace with user language
    f::load('languages' . DS . 'en.php');

  } 

}