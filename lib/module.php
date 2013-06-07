<?php

namespace Kirby\App;

use Kirby\Toolkit\Str;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Module
 * 
 * This is the base class for all modules. Extend this to create new modules 
 * and get basic module functionalities. Module classes are located in a 
 * dedicated module subfolder. Make sure the name of the module class file 
 * is identical with the module name. 
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Module {

  // a default title which should be passed to the layout
  protected $title = null;
  
  // the name of the module
  protected $name = null;

  // the relative path for the default layout
  protected $layout = null;
      
  // a list of all available controllers
  protected $controllers = null;
  
  // stores the current controller
  protected $controller = null;

  // the module file
  protected $file = null;

  /**
   * Register all routes for this module
   */
  public function routes() {
    return true;
  }

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
   * @param string $file Optional argument to use this as setter
   * @return string
   */
  public function file($file = null) {
    if(!is_null($file)) return $this->file = $file;
    return $this->file;
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
    return $this->controller = ($this->isActive()) ? app()->controller() : null;
  }

  /**
   * Checks if this module is currently active
   * 
   * @return boolean
   */
  public function isActive() {
    return app()->module()->name() == $this->name();
  }

  /**
   * Shortcut to work with snippets in controllers
   * 
   * @param string $path 
   * @param array $data
   * @param boolean $return
   * @return string
   */
  public function snippet($path, $data = array(), $return = true) {
    return view::snippet($path, $data, $return);
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
