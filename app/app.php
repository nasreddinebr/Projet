<?php
/*******************************************************************
* This file will contain the parameterization of Silex aplication. *
*******************************************************************/

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Rgister service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views',));
$app['twig'] = $app->extend('twig', function (Twig_Environment $twig, $app) {
	$twig->addExtension(new Twig_Extensions_Extension_Text());
	return $twig;
});
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\AssetServiceProvider(), array('assets.version' => 'v1'));
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
		'security.firewalls' => array(
				'secured' => array(
						'pattern' => '^/',
						'anonymous' => true,
						'logout' => true,
						'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
						'users' => function () use ($app) {
					return new BlogEcrivain\DAO\UserDAO($app['db']);
				},
			),
		),
		
		// Submit access to the back office to the ROLE_ADMIN role
		'security.role_hierarchy' => array(
				'ROLE_MODERATOR' => array('ROLE_USER'),
				'ROLE_AUTHOR' => array('ROLE_MODERATOR'),
				'ROLE_ADMIN' => array('ROLE_AUTHOR'),
				
		),
		'security.access_rules' => array(
				array('^/admin/moderator', 'ROLE_MODERATOR'),
				array('^/admin/comment', 'ROLE_MODERATOR'),
				array('^/admin/author', 'ROLE_AUTHOR'),
				array('^/admin/post', 'ROLE_AUTHOR'),
				//array('^/admin/post/{id}/edit', 'ROLE_AUTHOR'),
				array('^/admin', 'ROLE_ADMIN'),
		),		
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());

// Register services.
$app['dao.post'] = function($app) {
	$postDAO = new BlogEcrivain\DAO\PostDAO($app['db']);
	$postDAO->setUserDAO($app['dao.user']);
	return $postDAO;
};

$app['dao.user'] = function($app) {
	return new BlogEcrivain\DAO\UserDAO($app['db']);
};

// Inject dependence on the PostDAO class to the CommentDAO instance through the setPostDAO mutator.
$app['dao.comment'] = function($app) {
	$commentDAO = new BlogEcrivain\DAO\CommentDAO($app['db']);
	$commentDAO->setPostDAO($app['dao.post']);
	$commentDAO->setUserDAO($app['dao.user']);
	return $commentDAO;
};

$app['dao.media'] = function ($app) {
	return new BlogEcrivain\DAO\MediaDAO($app['db']);
};

// Register error handler
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
	switch ($code) {
		case 403:
			$message = 'Error: 403 Access denied.';
			break;
		case 404:
			$message = 'Error: 404 The requested resource could not be found.';
			break;
		default:
			$message = "Something went wrong.";
	}
	return $app['twig']->render('error.html.twig', array('message' => $message));
});
