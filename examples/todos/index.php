<?php

// load the kirby toolkit
require('../../../toolkit/bootstrap.php');

// load the kirby app framework
require('../../bootstrap.php');

// load the config
require('config.php');

// install modules
app::modules(__DIR__ . DS . 'modules');

// register an error handler for all major app errors
app::on('error', function($exception) {    
  echo response::error($exception);
});

// run the app
app::run();