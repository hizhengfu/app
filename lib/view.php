<?php

namespace Kirby\App;

use Kirby\Toolkit\Content;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Tpl;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * View
 * 
 * Views are located in each module in a dedicated views folder and each 
 * controller has its own subfolder with views for every action and request format.
 * You can pass any accessible data to the view object and the class will 
 * take care of rendering views and passing that data. 
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class View {

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
   * @param object $controller Controller
   */
  public function __construct($path, Controller $controller) {

    $this->path       = $path;
    $this->controller = $controller; 

    if(!file_exists($this->file())) raise('The view does not exist: ' . $this->file());

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
   * @return object Controller
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
    $module     = app()->modules()->get(strtolower($moduleName));  
    $format     = $this->controller()->format();

    if(!$module) raise('The module could not be found: ' . $moduleName);

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
    return $this->output = content::load($this->file(), $data);
  }

  /**
   * Includes a snippet from a smart path
   * 
   * @param string $path The smart path to the snippet
   * @param array $data Optional data, which should be passed to the snippet
   * @param boolean $return By default the snippet is echoed, but you can return the result by passing true here. 
   * @return string
   */
  static public function snippet($path, $data = array(), $return = false) {

    $parts      = str::split($path, '>');
    $moduleName = $parts[0]; 
    $module     = app()->modules()->get($moduleName);

    if(!$module) return false;

    $file = $module->root() . DS . 'snippets' . DS . $parts[1] . '.php';
    
    return tpl::loadFile($file, $data, $return);

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