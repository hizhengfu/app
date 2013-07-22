<?php

namespace Kirby\App;

use Kirby\Toolkit\Visitor;
use Kirby\App;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Redirect
 * 
 * Helps redirecting to various places in the app
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Redirect {

  /**
   * Redirects to a specific URL. You can pass either a normal URI
   * a controller path or simply nothing (which redirects home)
   * 
   * @param string $uri
   * @param array $arguments
   */
  static public function to($uri = '/', $arguments = array()) {
    go(app::url($uri, $arguments));
  }

  /**
   * Redirects to the home page of the app
   */
  static public function home() {
    go(app::url());
  }

  /**
   * Redirects to the last location of the user
   * 
   * @param string $fallback
   */
  static public function back($fallback = null) {
    // get the referer
    $referer = visitor::referer();
    // make sure there's a proper fallback
    if(empty($referer)) $referer = $fallback ? $fallback : app::url();
    go($referer);
  }

}