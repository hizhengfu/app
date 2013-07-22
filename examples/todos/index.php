<?php

// load the kirby toolkit
require('../../../toolkit/bootstrap.php');

// load the kirby app framework
require('../../bootstrap.php');

// define some constants
define('KIRBY_TODOS_ROOT',      __DIR__);
define('KIRBY_TODOS_ROOT_DATA', KIRBY_TODOS_ROOT . DS . 'data');

// install modules
app::modules(__DIR__ . DS . 'modules');

// register an error handler for all major app errors
app::on('error', function($error) {
  dump($error->message());
});

// run the app
app::run();