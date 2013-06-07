<?php

namespace Kirby\App;

use Kirby\Toolkit\Content;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Response
 * 
 * Represents any response coming from a controller's action and takes care of sending an appropriate header
 *  
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Response {

  // the response content
  protected $content;
  
  // the format type
  protected $format;

  /**
   * Constructor
   *
   * @param string $content
   * @param string $format
   */
  public function __construct($content, $format) {

    $this->content = $content;
    $this->format  = $format;

  }

  /**
   * Sends the correct header for the response
   */
  public function header($send = true) {
    return content::type($this->format, 'utf-8', $send);
  }

  /**
   * Returns the content of this response
   * 
   * @return string
   */
  public function content() {
    return $this->content;
  }

  /**
   * Returns the content format
   * 
   * @return string
   */
  public function format() {
    return $this->format;
  }

  /**
   * Echos the content
   * 
   * @return string
   */
  public function __toString() {
    return $this->content;
  }

}