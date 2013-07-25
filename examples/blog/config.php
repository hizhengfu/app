<?php
  
// connect the database
db::connect(array(
  'database' => __DIR__ . DS . 'data' . DS . 'blog.sqlite', 
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