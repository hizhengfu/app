<?php

namespace Kirby\App;

use Kirby\Toolkit\F;
use Kirby\Toolkit\HTML;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Assets
 * 
 * The assets class makes it easy to include css and js files from your modules in your views and layouts
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Assets {

  /**
   * Embeds css by url
   * 
   * @param string $url the relative or absolute url
   * @param string $media An optional media string
   * @return string
   */
  static public function css($url, $media = null) {
    
    if(empty($url)) return false;

    if(is_array($url)) {
      $css = array();
      foreach($url as $c) $css[] = self::css($c);
      return implode(PHP_EOL, $css);
    }

    // custom view css
    if($url === 'auto') {
      
      $module     = app()->module();
      $controller = app()->controller();
      $action     = app()->action();

      $path = $module . ' > assets' . DS . 'css' . DS . $controller . DS . $action . '.css';

      // load the custom css file if it exists
      return (f::exists(app()->root($path))) ? self::css($path) : null;

    }

    return html::stylesheet(app()->url($url), $media);

  }

  /**
   * Embeds js by url
   * 
   * @param string $url the relative or absolute url
   * @param boolean $async Optionally the js tag can include the new async attribute
   * @return string
   */
  static public function js($url, $async = false) {

    if(empty($url)) return false;

    if(is_array($url)) {
      $js = array();
      foreach($url as $j) $js[] = self::js($j);
      return implode(PHP_EOL, $js);
    }

    // custom view js
    if($url === 'auto') {
      
      $module     = app()->module();
      $controller = app()->controller();
      $action     = app()->action();

      $path = $module . ' > assets' . DS . 'js' . DS . $controller . DS . $action . '.js';

      // load the custom js file if it exists
      return (f::exists(app()->root($path))) ? self::js($path) : null;

    }

    return html::script(app()->url($url), $async);

  }

}