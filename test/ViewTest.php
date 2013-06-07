<?php

require_once('lib/bootstrap.php');

class ViewTest extends PHPUnit_Framework_TestCase {

  public function testSnippet() {

    $snippet = view::snippet('auth > test', array('test' => 'test'), $return = true);
    $this->assertEquals('this is a test', $snippet);


  }
 
}