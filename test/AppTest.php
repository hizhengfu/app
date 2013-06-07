<?php

require_once('lib/bootstrap.php');

class AppTest extends PHPUnit_Framework_TestCase {

  public function testMethods() {
    
    $this->assertInstanceOf('Kirby\\App\\App', app());
    $this->assertEquals('mysubfolder', app()->subfolder());
    $this->assertEquals('http://superurl.com/mysubfolder', app()->url());
    $this->assertEquals('http://superurl.com/mysubfolder/current', app()->uri()->toURL());
    $this->assertEquals('http', app()->scheme());
    $this->assertEquals('http://superurl.com/mysubfolder/some/path', app()->url('some/path'));

    $this->assertInstanceOf('Kirby\\App\\Modules', app()->modules());
    $this->assertTrue(app()->modules()->count() == 2);

  }
 
}