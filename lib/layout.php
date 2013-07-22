<?php

namespace Kirby\App;

use Kirby\Toolkit\Str;
use Kirby\App;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Layout
 * 
 * A layout is an extended view, which loads
 * a html layout skeleton from the layouts folder of a particular module.
 * You can use layouts to create boilerplates for complex html documents.
 * Views will become smaller if you outsource global stuff to layouts. 
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Layout extends View {

  // registered filters for layouts
  static public $filters = array();

  /**
   * Returns the full path to the layout file
   * 
   * @return string
   */
  public function file() {

    // make it possible to load a layout file directly
    if(file_exists($this->path)) return $this->path;

    // module > layout
    $path   = str::split($this->path, '>'); 
    $module = array_shift($path);
    $module = app::module($module);  

    return $module->root() . DS . 'layouts' . DS . implode(DS, $path) . '.' . $this->format . '.php';

  }

}