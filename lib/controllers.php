<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Controllers
 * 
 * A list of all available controllers
 * for a particular module
 * 
 * @package Kirby App
 */
class AppControllers extends Collection {

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
   * @return object KirbyAppModule
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
   * Returns the currently active controller if available
   * 
   * @return object KirbyAppController
   */
  public function findActive() {

    $uri        = app()->uri();  
    $controller = $uri->path(2);

    if(empty($controller) || $this->module()->singleController()) $controller = $this->module()->defaultController();

    $controller = $this->$controller;

    if(!$controller) app()->raise('Invalid controller');

    return $this->$controller;

  }

  /**
   * Returns the default controller if no other controller can be found
   * 
   * @return object KirbyAppController
   */
  public function findDefault() {
    $controller = $this->module()->defaultController();
    return $this->$controller;
  }

  /**
   * Echos a list of all available controllers
   * 
   * @return string
   */
  public function __toString() {
    $html = array();
    foreach($this->_ as $controller) $html[] = (string)$controller;
    return implode('<br />', $html);
  }

}