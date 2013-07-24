<?php

namespace Kirby\App;

use Kirby\App;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Template;

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
class View extends Template {

  // global variables for all views
  static public $globals = array();

  // registered filters for views
  static public $filters = array();

  // custom root for the view
  static public $root = null;

  /**
   * Returns the view file
   * 
   * @return string
   */
  public function file() {

    // fallback for the format 
    if(empty($this->format)) $this->format = 'html';

    // create a view for the current controller action
    if(is_a($this->path, 'Kirby\\App\\Controller')) {
      $controller = $this->path;
      $module     = $controller->module();

      // build the file path
      $this->path   = $module->name() . ' > ' . $controller->name() . ' > ' . $controller->action();
      $this->format = $controller->format();
    } 

    if(is_file($this->path)) {
      return $this->path;
    } else {
      $path   = str::split($this->path, '>'); 
      $module = array_shift($path);
      $module = app::module($module);  

      return $module->root() . DS . 'views' . DS . implode(DS, $path) . '.' . $this->format . '.php';
    }

  }

}