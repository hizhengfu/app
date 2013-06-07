<?php

namespace Kirby\App;

use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Dir;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Modules
 * 
 * A list of all available modules for the app. 
 * You can use this to access any module and any controller in a module. 
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

        $this->set(strtolower($module), $object);
      }
    
    }

  }

  /**
   * Echos a list of all available modules
   * 
   * @return string
   */
  public function __toString() {
    $html = array();
    foreach($this->data as $module) $html[] = (string)$module;
    return implode('<br />', $html);
  }

}
