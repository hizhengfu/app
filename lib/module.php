<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Module
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class AppModule {

  // a default title which should be passed to the layout
  protected $title = null;
  
  // the name of the module
  protected $name = null;

  // the relative path for the default layout
  protected $layout = 'shared > default';
  
  // the relative path for the default controller
  protected $defaultController = 'home';
  
  // there can be single controller or multi controller modules
  protected $singleController = false;
  
  // a list of all available controllers
  protected $controllers = null;
  
  // stores the current controller
  protected $controller = null;

  // the module file
  protected $file = null;

  // show the module in menus 
  protected $visible = true;

  /**
   * Returns the module name
   * 
   * @return string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Returns the default title for this module, which 
   * should be passed to the layout
   * 
   * @return string
   */
  public function title() {
    return !empty($this->title) ? $this->title : str::ucfirst($this->name);
  }

  /**
   * Returns the default layout object
   * 
   * @return object Layout
   */
  public function layout() {
    return $this->layout;
  }

  /**
   * Returns the full path to the module file
   * 
   * @return string
   */
  public function file($file = null) {
    if(!is_null($file)) return $this->file = $file;
    return $this->file;
  }

  /**
   * Returns the main module url
   * 
   * @return string
   */
  public function url($path = '') {
    return rtrim(app()->url() . '/' . $this->name() . '/' . $path, '/');
  }

  /**
   * Returns the module directory root
   * 
   * @return string
   */
  public function root() {
    return dirname($this->file);    
  }

  /**
   * Returns a list with all available controllers for this module
   * 
   * @return object Controllers
   */
  public function controllers() {
    if(!is_null($this->controllers)) return $this->controllers;
    return $this->controllers = new Controllers($this);
  }

  /**
   * Returns the currently active controller
   * 
   * @return object Controller
   */
  public function controller() {
    if(!is_null($this->controller)) return $this->controller;
    return $this->controller = $this->controllers()->findActive();
  }

  /**
   * Returns the name of the default controller for this module
   * 
   * @return string
   */
  public function defaultController() {
    return $this->defaultController;
  }

  /**
   * Checks if this is a single controller module
   * 
   * @return boolean
   */
  public function singleController() {
    return $this->singleController;
  }

  /**
   * Checks if this controller is currently active
   * 
   * @return boolean
   */
  public function isActive() {

    $uri    = app()->uri();  
    $module = $uri->path(1);

    if(empty($module)) $module = app()->defaultModule();

    return $module == $this->name() ? true : false;

  }

  /**
   * Checks if this controller is visible
   * 
   * @return boolean
   */
  public function isVisible() {
    return $this->visible;
  }

  /**
   * Shortcut to return snippets in controller actions
   *
   * @param string $snippet The name of the snippet
   * @param array $data Optional data for the snippet
   * @return string
   */
  public function snippet($snippet, $data = array()) {
    return app()->snippet($snippet, $data, $return = true);
  }

  /**
   * Echos the name of the controller
   * 
   * @return string
   */
  public function __toString() {
    return $this->name();
  }

}
