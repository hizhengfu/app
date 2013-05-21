<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Controller
 * 
 * The main controller class, which will be used as 
 * base for all custom controllers
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Controller {

  // store for all controller data, which will be passed to the view
  protected $data = array();
  
  // the parent list of controllers
  protected $siblings = null;

  // the parent module
  protected $module = null;
  
  // the current action
  protected $action = 'index';
    
  // the response format
  protected $format = null;
  
  // the controller file
  protected $file = null;
  
  // the assigned layout
  protected $layout = null;
  
  // the assigned view
  protected $view = null;

  /**
   * Constructor
   * 
   * @param string $file The full path to the controller file
   * @param object Controllers a list of sibling controllers
   */
  public function __construct($file, Controllers $siblings) {

    $this->file     = $file;
    $this->siblings = $siblings;
    $this->module   = $siblings->module();
    $this->layout   = $this->layout();

  }

  /**
   * Returns all assigned data 
   * 
   * @return array
   */
  public function data() {
    return $this->data;
  }

  /**
   * Returns the name of the controller
   * 
   * @return string
   */
  public function name() {
    return f::name($this->file, true);
  }

  /**
   * Returns the full file path of the controller
   *
   * @return string
   */
  public function file() {
    return $this->file;
  }

  /**
   * Magic setter for new data
   * 
   * @param string $property is set by php 
   * @param mixed $value 
   */
  public function __set($property, $value) {
    $this->set($property, $value);
  }

  /**
   * Sets new data
   * 
   * @param string $property
   * @param mixed $value
   */
  public function set($property, $value) {
    $this->data[$property] = $value;  
    return $this;
  }

  /**
   * Magic getter for data
   * 
   * @param string $property set by php
   * @return mixed
   */
  public function __get($property) {
    return $this->get($property);
  }

  /**
   * Magic getter method for data
   * 
   * @param string $property set by php
   * @param mixed $arguments optional arguments. not used!
   * @return mixed
   */
  public function __call($property, $arguments = null) {
    return $this->get($property);
  }

  /**
   * Getter for stored data
   *
   * @param string $property
   * @return mixed
   */
  public function get($property) {
    return isset($this->data[$property]) ? $this->data[$property] : null;
  }

  /**
   * Returns the parent module
   * 
   * @return object AppModule
   */
  public function module() {
    return $this->module;
  }

  /**
   * Returns the response format type
   * 
   * @return string
   */
  public function format($format = null) {
    
    if(!is_null($format)) $this->format = $format;

    if(is_null($this->format)) {  
      $extension = app()->uri()->extension();
      $this->format = (empty($extension) || $extension == 'php') ? 'html' : $extension;
    }
    
    return $this->format;
  
  }

  /**
   * Returns the assigned layout object
   * 
   * @param string $path Smart path to change the layout
   * @return object Layout
   */
  public function layout($path = null) {
        
    if(!is_null($path)) $this->layout = new Layout($path, $this);
    if(!is_null($this->layout)) return $this->layout;
    
    return $this->layout = new Layout($this->module->layout(), $this);

  }

  /**
   * Returns the view object
   * 
   * @param string $path Smart path to change the view
   * @return object View
   */
  public function view($path = null) {

    if(!is_null($path)) return $this->view = new View($path, $this);
    if(!is_null($this->view)) return $this->view;
        
    return $this->view($this->module()->name() . ' > ' . $this->name() . ' > ' . $this->action());

  }

  /**
   * Stores a flash message to be re-used in the next request
   * 
   * @param string $type a type for the flash message if you want to use this as setter. This makes it possible to store different flash messages for different types of stuff (error, notice, etc.)
   * @param string $message The message which should be stored
   * @return mixed If no type is specified this will return the last message
   */
  public function flash($type = false, $message = false) {
    return app()->flash($type, $message);  
  }
 
  /**
   * Redirects to a different path and optionally stores a flash message
   * 
   * @param string $path A relative path to redirect to
   * @param array $params Optional params to store a flash message
   */
  public function redirect($path = '', $params = array()) {
    
    $defaults = array(
      'notice' => false
    );
          
    $options = array_merge($defaults, $params);
    
    if($options['notice']) {
      $this->flash('notice', $options['notice']);
    }
                    
    go(app()->url($path));

  }

  /**
   * Checks if a form has been submitted
   * The request method and csfr must match
   *
   * @param string $method The request method to check for
   * @return boolean
   */
  public function submitted($method = 'post') {
    return (r::is($method) && app()->csfr(r::get('csfr'))) ? true : false;
  }

  /**
   * Respond with a json status object
   * No view will be rendered afterwards
   * 
   * @param mixed $type Either success, error or a saved model
   * @param string $message An error or success message
   * @param array $data Optional data for the response
   * @return string
   */
  public function respond($type, $message, $data = array()) {

    // if an entire model is passed, check for failures and build an auto status
    if(is_a($type, 'model')) {

      if($type->valid()) {
        return $this->success($message);        
      } else {
        return $this->error($type->error());
      }

    }

    $defaults = array(
      'status'  => $type,
      'message' => $message,
    );    
    die(json_encode(array_merge($defaults, $data)));
  }

  /**
   * Responds with a json success status
   * 
   * @param string $message The success message
   * @param array $data optional data to merge into the response array
   * @return string
   */
  public function success($message = 'Yay!', $data = array()) {
    $this->respond('success', $message, $data);
  }

  /**
   * Responds with a json error status
   * 
   * @param string $message The error message
   * @param array $data optional data to merge into the response array
   * @return string
   */
  public function error($message = 'Oh no!', $data = array()) {
    $this->respond('error', $message, $data);
  }

  /**
   * Returns the currently selected action 
   * 
   * @param string $action Optional argument to use this as a setter
   * @return string Name of the action
   */
  public function action($action = null) {
    if(!is_null($action)) return $this->action = $action;
    return $this->action;
  }

  /**
   * Calls the current action and returns the response generated by this controller
   * 
   * @return object Response
   */
  public function call($action, $options = array()) {

    // set the action
    $this->action = $action;

    // run the action
    call_user_func_array(array($this, $action), $options);

    // get the current layout
    $layout = $this->layout();
    
    // apply the content 
    $layout->content = $this->view()->render();

    // return the response object       
    return new Response($layout->render(), $this->format());

  }

  /**
   * Shortcut to work with snippets in controllers
   * 
   * @param string $path 
   * @param array $data
   * @param boolean $return
   * @return string
   */
  public function snippet($path, $data = array(), $return = true) {
    return view::snippet($path, $data, $return);
  }

  /**
   * Debugger method to echo the controller's name
   * 
   * @return string
   */
  public function __toString() {
    return $this->name();
  }

}