<?php 
require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();

// Enable the debug mode
$app['debug'] = true;

require __DIR__.'/../app/routes.php';
$app->run();
