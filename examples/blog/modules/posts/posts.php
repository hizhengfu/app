<?php

class PostsModule extends Module {

  public function routes() {
    
    route::register(array(
      '/' => array(
        'action' => 'posts > posts::index',
        'method' => 'GET'
      ),
      'write' => array(
        'action' => 'posts > posts::add',
        'method' => 'GET|POST'
      ),
      '(:any)' => array(
        'action' => 'posts > posts::show',
        'method' => 'GET|POST'
      ),
      '(:any)/delete' => array(
        'action' => 'posts > posts::delete',
        'method' => 'GET'
      )
    ));

  }

  public function config() {
  
    layout::filter('posts > application', function($layout) {
      $layout->title = 'Blog';
    });

  }

}