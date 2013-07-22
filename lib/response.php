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
    return (string)$this->content;
  }

  /**
   * Returns a success response 
   * 
   * @return object
   */
  static public function success($message = 'Everything went fine', $data = array(), $code = 200) {
    return new static(json_encode(array(
      'status'  => 'success',
      'code'    => $code,
      'message' => $message, 
      'data'    => $data
    )), 'json');
  }

  /**
   * Returns an error response   
   * 
   * @return object
   */
  static public function error($message = 'Something went wrong', $data = array(), $code = 400) {
  
    if(is_a($message, 'Kirby\\Toolkit\\Error')) {      
      $code    = $message->code();
      $data    = $message->data();
      $message = $message->message();
    }

    return new static(json_encode(array(
      'status'  => 'error',
      'code'    => $code,
      'message' => $message, 
      'data'    => $data
    )), 'json');

  }

  /**
   * Converts an array to json and returns it properly
   * 
   */
  static public function json($array) {
    return new static(json_encode($array), 'json');
  }

}