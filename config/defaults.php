<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * This file sets all default options for the Kirby app
 * 
 * DON'T OVERWRITE OPTIONS HERE!!!
 *
 * Include your custom config file to overwrite
 * config variables on runtime 
 * 
 * Changing stuff in this file might break 
 * your Kirby app installation, since those are the 
 * fallback values!
 * 
 * @package   Kirby App
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
c::set(array(
  
  /**
   * human readable app version 
   */
  'app.version.string' => '1.0',

  /**
   * numeric app version 
   */
  'app.version.number' => 1.0,
  
  /**
   * if the app is installed in a subfolder
   * and the subfolder is not detected correctly
   * set the subfolder here
   */
  'app.subfolder' => '@auto',

));