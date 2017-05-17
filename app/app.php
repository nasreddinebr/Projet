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
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views',));
$app->register(new Silex\Provider\AssetServiceProvider(), array('assets.version' => 'v1'));


// Register services.
$app['dao.post'] = function ($app) {
	return new BlogEcrivain\DAO\PostDAO($app['db']);
};
//Inject dependence on the PostDAO class to the CommentDAO instance through the setPostDAO mutator.
$app['dao.comment'] = function ($app) {
	$commentDAO = new BlogEcrivain\DAO\CommentDAO($app['db']);
	$commentDAO->setPostDAO($app['dao.post']);
	return $commentDAO;
};