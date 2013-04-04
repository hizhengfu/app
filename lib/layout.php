<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Layout
 * 
 * A layout is an extended view, which loads
 * a html layout skeleton from the layouts folder of a particular module.
 * 
 * @package Kirby App
 */
class AppLayout extends AppView {

  /**
   * Constructor
   * 
   * @param string $path The path to this layout
   * @param object $controller The parent KirbyAppController
   */
  public function __construct($path, AppController $controller) {

    $this->path          = $path;
    $this->controller    = $controller; 
    $this->data          = array();
    $this->data['title'] = $this->controller->module()->title();

    if(!file_exists($this->file())) app()->raise('The layout does not exist: ' . $this->file());

  }

  /**
   * Returns the proper layout file path
   * 
   * @return string
   */
  public function file() {
    
    // site > default
    $path       = str::split($this->path, '>'); 
    $moduleName = array_shift($path);
    $module     = app()->modules()->get($moduleName);  
    $format     = $this->controller()->format();

    if(!$module) app()->raise('The module could not be found: ' . $moduleName);

    $file = $module->root() . DS . 'layouts' . DS . implode(DS, $path) . '.' . $format . '.php';

    return $this->file = $file;
  
  }

}