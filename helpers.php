<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/** 
 * Singleton handler for the app object
 * Always use this to initiate the app!
 * 
 * @return object App
 */
function app($params = array()) {
  $app = g::get('app');
  if(!$app || !empty($params)) {
    $class = KIRBY_APP_CLASS;
    $app   = new $class($params);
    g::set('app', $app);
  }
  return $app;
}

/**
 * Singleton getter for the current module
 * 
 * @return object Module
 */
function module() {
  return app()->module();  
}

/**
 * Singleton getter for the current controller
 * 
 * @return object Controller
 */
function controller() {
  return module()->controller();
}