<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Response
 *  
 * @package Kirby App
 */
class AppResponse {

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

    $this->header();

  }

  /**
   * Sends the correct header for the response
   */
  public function header() {

    $formats = c::get('app.response.formats');
    $mime    = a::get($formats, $this->format);

    if($mime) header('Content-type: ' . $mime);

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