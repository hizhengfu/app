<?php

// load the kirby toolkit
(@include('../../../toolkit/bootstrap.php')) or die('The Kirby toolkit is required');

// load the kirby app framework
(@include('../../bootstrap.php')) or die('The Kirby app framework is required');

// load the config
require('config.php');

// install modules
app::modules(__DIR__ . DS . 'modules');

// run the app
app::run();