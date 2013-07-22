<?php

namespace Kirby\App;

use Exception;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Content;
use Kirby\Toolkit\Str;
use Kirby\App;

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

  // registered filters for views
  static public $filters = array();

  // all data assigned to the view
  public $data = array();

  // the view format
  public $format = 'html';

  // the path to this view
  public $path = null;

  /**
   * Static instance creator
   * 
   * @param mixed $path 
   * @param array $data
   * @param string $format
   * @return object
   */
  static public function create($path, $data = array(), $format = 'html') {
    return new static($path, $data, $format);
  }

  /**
   * Constructor
   * 
   * @param mixed $path 
   * @param array $data
   * @param string $format
   * @return object
   */
  public function __construct($path, $data = array(), $format = 'html') {

    $this->data   = $data;
    $this->path   = $path;
    $this->format = $format; 

    // create a view for the current controller action
    if(is_a($this->path, 'Kirby\\App\\Controller')) {
      $controller = $this->path;
      $module     = $controller->module();

      // build the file path
      $this->path   = $module->name() . ' > ' . $controller->name() . ' > ' . $controller->action();
      $this->format = $controller->format();
    }

    // call a view filter for this view, if available
    if(array_key_exists($this->path, static::$filters) and is_callable(static::$filters[$this->path])) {
      call_user_func_array(static::$filters[$this->path], array($this));
    }

  }

  /**
   * Register a view filter
   * 
   * @param string $path
   * @param closure $callback
   */
  static public function filter($path, $callback) {
    static::$filters[$path] = $callback;
  }

  /**
   * Magic setter for view variables
   * 
   * @param string $key
   * @param mixed $value
   */
  public function __set($key, $value) {
    $this->data[$key] = $value;
  }

  /**
   * Magic getter for view variables
   * 
   * @param string $key
   * @return mixed
   */
  public function __get($key) {
    return a::get($this->data, $key);
  }

  /**
   * Checks if a view variable is set
   * 
   * @param string $key
   * @return boolean
   */
  public function __isset($key) {
    return isset($this->data[$key]);
  }

  /**
   * Returns the full path to the view file
   * 
   * @return string
   */
  public function file() {

    if(file_exists($this->path)) {
      return $this->path;
    } else {

      $path   = str::split($this->path, '>'); 
      $module = array_shift($path);
      $module = app::module($module);  

      return $module->root() . DS . 'views' . DS . implode(DS, $path) . '.' . $this->format . '.php';

    }

  }

  /**
   * Renders the view and passes all data to the template
   * 
   * @param string $format Optional way to pass the view format
   * @return string
   */
  public function render($format = null) {

    // overwrite the format
    if(!is_null($format)) $this->format = $format;
    
    // get the file 
    $file = $this->file();
    
    // check if the view file exists
    if(!file_exists($file)) raise('The view does not exist: ' . $file);
    
    // load the content
    return content::load($file, $this->data);

  }

  /**
   * Makes it possible to echo the view and get back all its content that way
   * 
   * @return string
   */
  public function __toString() {
    try {
      return $this->render();
    } catch(Exception $e) {
      return '';
    }
  }

}

