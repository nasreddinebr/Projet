<?php
/*******************************************************************
* This file will contain the parameterization of Silex aplication. *
*******************************************************************/

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Rgister service provider.
$app->register(new Silex\Provider\DoctrineServiceProvider());

// Register services.
$app['dao.post'] = function ($app) {
	return new BlogEcrivain\DAO\PostDAO($app['db']);
};