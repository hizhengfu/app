<?php

// define some constants
define('KIRBY_BLOG_ROOT',      __DIR__);
define('KIRBY_BLOG_ROOT_DATA', KIRBY_BLOG_ROOT . DS . 'data');

try {
  
  // connect the database
  db::connect(array(
    'database' => KIRBY_BLOG_ROOT_DATA . DS . 'blog.sqlite', 
    'type'     => 'sqlite'
  ));

  // create the todos table
  db::query('

    CREATE TABLE IF NOT EXISTS "posts" (
      "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
      "title" TEXT,
      "text" TEXT,
      "slug" TEXT,
      "added" INTEGER
    );

  ');

  db::query('

    CREATE TABLE IF NOT EXISTS "comments" (
      "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
      "post" INTEGER NOT NULL,
      "text" TEXT,
      "name" TEXT,
      "email" TEXT, 
      "added" INTEGER
    );

  ');

} catch(Exception $e) {
  app::raise('db-error', $e->getMessage());
}