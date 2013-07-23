<?php

namespace Kirby\App;

use Kirby\Toolkit\Autoloader;

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

  // the module's name
  public $name;
  
  // the module class file
  public $file;
  
  // the module root directory
  public $root;

  /**
   * Contstructor
   */
  public function __construct($file) {

    // set the module file
    $this->file = $file;

    // get the name of this module
    $this->name = str_replace('module', '', strtolower(get_called_class()));
        
    // get the module root
    $this->root = dirname($this->file);
  
  }

  /**
   * Returns the name of the module
   * 
   * @return string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Returns the file path of the module class
   * 
   * @return string
   */
  public function file() {
    return $this->file;
  }

  /**
   * Returns the root directory of the module
   * 
   * @return string
   */
  public function root() {
    return $this->root;
  }

  /**
   * Registers all routes for this module
   */
  public function routes() {
    // Example:
    // router::get('/', 'mymodule > mycontroller::index');
  }

  /**
   * Returns the default layout for all controllers in this module
   */
  public function layout() {
    return null;
  }

  /**
   * Setup autoloading models
   */
  public function autoloader() {

    // start autoloading all models
    $autoloader = new Autoloader();
    $autoloader->root = $this->root() . DS . 'models';
    $autoloader->start();

  }

  /**
   * Custom load event
   */
  public function load() {}

  /**
   * Custom config event
   */
  public function config() {}

  /**
   * Run event, which can be overwritten by child classes
   */
  public function run() {}

  /**
   * Makes it possible to echo the module object
   * or use it in smart strings like $this . ' > whatever'
   */
  public function __toString() {
    return $this->name();
  }

}