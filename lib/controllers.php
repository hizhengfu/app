<?php

namespace Kirby\App;

use Kirby\Toolkit\Dir;
use Kirby\Toolkit\Collection;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Controllers
 * 
 * A list of all available controllers for a particular module.
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Controllers extends Collection {

  // The parent module
  protected $module = null;
  
  // The controllers directory
  protected $root = null;

  /**
   * Constructor
   * 
   * @param object Module The parent module object
   */
  public function __construct($module) {
    $this->module = $module;
    $this->root   = $this->module->root() . DS . 'controllers';

    if(!is_dir($this->root)) return false;
    
    $controllers = dir::read($this->root);

    foreach($controllers as $controller) {

      $file  = $this->root . DS . $controller;
      $name  = str_replace('.php', '', $controller);
      $class = $name . 'controller';

      if(file_exists($file)) {
        require_once($file);
        $this->set($name, new $class($file, $this));
      }

    }

  }

  /**
   * Returns the parent module object
   * 
   * @return object Module
   */
  public function module() {
    return $this->module;
  }

  /**
   * Returns the root of the controllers dir
   * 
   * @return string
   */
  public function root() {
    return $this->root;
  }

  /**
   * Echos a list of all available controllers
   * 
   * @return string
   */
  public function __toString() {
    $html = array();
    foreach($this->data as $controller) $html[] = (string)$controller;
    return implode('<br />', $html);
  }

}