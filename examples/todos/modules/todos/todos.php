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

  public function config() {

    try {
      
      // connect the database
      db::connect(array(
        'database' => KIRBY_TODOS_ROOT_DATA . DS . 'todos.sqlite', 
        'type'     => 'sqlite'
      ));

      // create the todos table
      db::query('

        CREATE TABLE IF NOT EXISTS "todos" (
          "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
          "text" TEXT,
          "done" INTEGER
        );

      ');

    } catch(Exception $e) {
      app::raise('db-error', $e->getMessage());
    }

  }

}