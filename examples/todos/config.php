<?php
  
// connect the database
db::connect(array(
  'database' => __DIR__ . DS . 'data' . DS . 'todos.sqlite', 
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