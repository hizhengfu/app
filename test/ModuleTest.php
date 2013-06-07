<?php

require_once('lib/bootstrap.php');

class ModuleTest extends PHPUnit_Framework_TestCase {

  public function testGetters() {

    $module = app()->modules()->auth();
  
    $this->assertEquals('Authentication', $module->title());
    $this->assertEquals('Auth', $module->name());
    $this->assertEquals('auth > login', $module->layout());
    $this->assertEquals(TEST_ROOT_ETC . DS . 'modules' . DS . 'auth', $module->root());
    $this->assertEquals(TEST_ROOT_ETC . DS . 'modules' . DS . 'auth' . DS . 'auth.php', $module->file());

    $module = app()->modules()->users();

    $this->assertEquals('Users', $module->title());
    $this->assertEquals('Users', $module->name());
    $this->assertEquals('shared > application', $module->layout());
    $this->assertEquals(TEST_ROOT_ETC . DS . 'modules' . DS . 'users', $module->root());
    $this->assertEquals(TEST_ROOT_ETC . DS . 'modules' . DS . 'users' . DS . 'users.php', $module->file());

  }
 
}