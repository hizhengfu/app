<?php

require_once('lib/bootstrap.php');

class ControllerTest extends PHPUnit_Framework_TestCase {

  public function testStuff() {

    $module      = app()->modules()->auth();
    $controllers = $module->controllers();

    $this->assertInstanceOf('Kirby\\App\\Controllers', $controllers);
    $this->assertEquals(2, $controllers->count());
    $this->assertEquals($module, $controllers->module());
    $this->assertEquals($module->root() . DS . 'controllers', $controllers->root());

    $controller = $controllers->first();
    $this->assertInstanceOf('Kirby\\App\\Controller', $controller);

    $this->assertEquals('login', $controller->name());
    $this->assertEquals('login', (string)$controller);
    $this->assertEquals($controllers->root() . DS . 'login.php', $controller->file());
    $this->assertTrue(is_array($controller->data()));
    $this->assertEquals($module, $controller->module());
    $this->assertEquals('html', $controller->format());

    $layout = $controller->layout();

    $this->assertInstanceOf('Kirby\\App\\Layout', $layout);

    // call one of the actions and return the response object
    $response = $controller->call('index');

    $this->assertInstanceOf('Kirby\\App\\Response', $response);
    $this->assertEquals('login', $response->content());
    $this->assertEquals('html', $response->format());
    $this->assertEquals('Content-type: text/html; charset=utf-8', $response->header($send = false));

  }
 
}