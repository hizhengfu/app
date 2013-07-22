<?php

class TodosModule extends Module {

  public function routes() {

    route::register(array(
      '/' => array(
        'action' => 'todos > todos::index',
        'method' => 'GET'
      ),
      'add' => array(
        'action' => 'todos > todos::add',
        'method' => 'POST'
      ),
      '(:num)/done' => array(
        'action' => 'todos > todos::done',
        'method' => 'POST'
      ),
      '(:num)/undone' => array(
        'action' => 'todos > todos::undone',
        'method' => 'POST'
      ),
      '(:num)/delete' => array(
        'action' => 'todos > todos::delete',
        'method' => 'GET'
      ),
    ));

  }

}