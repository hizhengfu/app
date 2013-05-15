<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Modules
 * 
 * A list of all available modules for the app
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Modules extends Collection {

  /**
   * Constructor
   */
  public function __construct() {

    $modules = dir::read(KIRBY_APP_ROOT_MODULES);

    foreach($modules as $module) {
      
      $file  = KIRBY_APP_ROOT_MODULES . DS . $module . DS . $module . '.php';
      $class = $module . 'module';

      if(file_exists($file)) {
        require_once($file);

        $object = new $class();
        $object->file($file);

        $this->set($module, $object);
      }
    
    }

  }

  /**
   * Returns all visible modules
   * 
   * @return object Modules
   */
  public function visible() {
    return $this->filterBy('isVisible', true);
  }

  /**
   * Returns the currently active module
   * 
   * @return object Module
   */
  public function findActive() {

    $uri    = app()->uri();  
    $module = $uri->path(1);

    if(empty($module)) {
      $module = app()->defaultModule();
    } else {
      $module = $this->$module;
    }

    if(!$module) app()->raise('Invalid module: ' . $module);

    return $this->$module;

  }

  /**
   * Returns the default module, which should be used if no other is set in the url
   * 
   * @return object Module
   */
  public function findDefault() {
    $module = app()->defaultModule();
    return $this->$module;
  }

  /**
   * Echos a list of all available modules
   * 
   * @return string
   */
  public function __toString() {
    $html = array();
    foreach($this->_ as $module) $html[] = (string)$module;
    return implode('<br />', $html);
  }

}
