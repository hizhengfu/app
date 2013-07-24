<?php

namespace Kirby\App;

use Kirby\App;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Template;

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
class Layout extends Template {

  // registered filters for layouts
  static public $filters = array();

  // the custom layout root
  static public $root = null;

  /**
   * Returns the layout file
   * 
   * @return string
   */
  public function file() {

    // make it possible to load a layout file directly
    if(is_file($this->path)) return $this->path;

    // fallback for the format
    if(empty($this->format)) $this->format = 'html';

    // module > layout
    $path   = str::split($this->path, '>'); 
    $module = array_shift($path);
    $module = app::module($module);  
    $root   = $module->root() . DS . 'layouts' . DS . implode(DS, $path) . '.' . $this->format . '.php';

    return (is_file($root)) ? $root : parent::file();

  }

}