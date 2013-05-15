<?php

require_once('lib/bootstrap.php');

class AppTest extends PHPUnit_Framework_TestCase {

  public function __construct() {

    $this->app = app(array(
      'app.subfolder'  => 'mysubfolder',
      'app.url'        => 'http://superurl.com',
      'app.currentURL' => 'http://superurl.com/mysubfolder/current',
    ));
    
  }

  public function testMethods() {
    
    $this->assertEquals('mysubfolder', $this->app->subfolder());
    $this->assertEquals('http://superurl.com/mysubfolder', $this->app->url());
    $this->assertEquals('http://superurl.com/mysubfolder/current', $this->app->uri()->toURL());
    $this->assertEquals('http', $this->app->scheme());
    $this->assertEquals('http://superurl.com/mysubfolder/some/path', $this->app->url('some/path'));

    $this->assertInstanceOf('Modules', $this->app->modules());
    $this->assertTrue($this->app->modules()->count() == 0);

  }
 
}