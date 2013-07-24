<?php

namespace Kirby\App;

use Kirby\App;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Template;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Snippet
 * 
 * A snippet is a code partial, which can be shared between
 * views and layouts. Snippets are located in the {module}/snippets folder
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Snippet extends Template {

  // global variables for all snippets
  static public $globals = array();

  // registered filters for layouts
  static public $filters = array();

  // the custom root handler
  static public $root = null;

  /**
   * Returns the full path to the snippet file
   * 
   * @return string
   */
  public function file() {

    // make it possible to load a layout file directly
    if(is_file($this->path)) return $this->path;

    // module > layout
    $path   = str::split($this->path, '>'); 
    $module = array_shift($path);
    $module = app::module($module);  

    return $module->root() . DS . 'snippets' . DS . implode(DS, $path) . '.php';

  }

}