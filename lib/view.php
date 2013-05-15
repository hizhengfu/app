<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * View
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class AppView {

  // all data assigned to the view
  protected $data = array();
  
  // the parent controller object
  protected $controller;
  
  // the path to this view
  protected $path;
  
  // the full path to the view file
  protected $file;

  /**
   * Constructor
   * 
   * @param string $path
   * @param object $controller AppController
   */
  public function __construct($path, AppController $controller) {

    $this->path       = $path;
    $this->controller = $controller; 

    if(!file_exists($this->file())) app()->raise('The view does not exist: ' . $this->file());

  }

  /**
   * Magic setter for assigned data
   * 
   * @param string $property
   * @param mixed $value
   */
  public function __set($property, $value) {
    $this->data[$property] = $value;
  }

  /**
   * Magic getter for assigned data
   * 
   * @param string $property
   * @return mixed
   */
  public function __get($property) {
    return isset($this->data[$property]) ? $this->data[$property] : null;
  }

  /**
   * Magic isset checker for assigned data
   * 
   * @param string $property
   * @return boolean
   */
  public function __isset($property) {
    return isset($this->data[$property]);  
  }

  /**
   * Returns all the assigned data
   * 
   * @return array
   */
  public function data() {
    return $this->data;
  }

  /**
   * Returns the parent controller object
   * 
   * @return object KirbyAppController
   */
  public function controller() {
    return $this->controller;
  }

  /**
   * Returns the full file path
   *
   * @return string
   */
  public function file() {
    
    // site > dashboard > edit
    $path       = str::split($this->path, '>'); 
    $moduleName = array_shift($path);
    $module     = app()->modules()->get($moduleName);  
    $format     = $this->controller()->format();

    if(!$module) app()->raise('The module could not be found: ' . $moduleName);

    $file = $module->root() . DS . 'views' . DS . implode(DS, $path) . '.' . $format . '.php';

    return $this->file = $file;
  
  }
     
  /**
   * Renders the view and returns the html
   * 
   * @return string
   */
  public function render() {

    $data = array_merge($this->data, $this->controller->data());
      
    ob_start();
    extract($data);
    require_once($this->file());    
    $this->output = ob_get_contents();    
    ob_end_clean();    
    
    return $this->output;
            
  }
 
  /**
   * Echos the html for this view
   * 
   * @return string
   */
  public function __toString() {
    return $this->render();
  }

}