<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Set the home url
 */
event::on('kirby.toolkit.url.home', function(&$url) {
  $url = app::uri()->baseurl();
});

/**
 * Smarter url::to(), url() and u()
 * You can pass route names or action strings
 */
event::on('kirby.toolkit.url.to', function(&$url, $arguments = array()) {
  array_unshift($arguments, $url);
  $url = call_user_func_array(array('app', 'url'), $arguments);
});

/**
 * Enables smart auto-loading of stylesheets
 * by module, controller and action
 */
event::on('kirby.toolkit.html.stylesheet', function(&$href, &$media = null, &$attr = array()) {

  if($href != '@auto') return false;
    
  $module     = app::module();
  $controller = app::controller();
  $action     = app::action();

  $path = $controller->name() . DS . $action . '.css';
  $file = $module->root() . DS . 'assets' . DS . 'css' . DS . $path;

  if(!file_exists($file)) raise('The css file does not exist');

  $href = 'assets/css/' . $module->name() . '/' . $path;

});

/**
 * Enables smart auto-loading of javascript files
 * by module, controller and action
 */
event::on('kirby.toolkit.html.script', function(&$src, &$attr = array()) {

  if($href != '@auto') return false;
    
  $module     = app::module();
  $controller = app::controller();
  $action     = app::action();

  $path = $controller->name() . DS . $action . '.js';
  $file = $module->root() . DS . 'assets' . DS . 'js' . DS . $path;

  if(!file_exists($file)) raise('The javascrip file does not exist');

  $href = 'assets/js/' . $module->name() . '/' . $path;

});

/**
 * Register the default error event
 */
event::on('kirby.app.error', function($exception) {
  echo $exception;
});