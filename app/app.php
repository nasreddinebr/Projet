<?php
/*******************************************************************
* This file will contain the parameterization of Silex aplication. *
*******************************************************************/

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

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
				'logout'	=> true,
				'form'		=> array('login_path' => '/login', 'check_path' => '/login_check'),
				'users'		=> function () use ($app) {
					return new BlogEcrivain\DAO\UserDAO($app['db']);
				},
			),
		),
		// Submit access to the back office to the ROLE_ADMIN role
		'security.role_hierarchy' => array(
				'ROLE_ADMIN' => array('ROLE_USER'),
		),
		'security.access_rules' => array(
				array('^/admin', 'ROLE_ADMIN'),
		),
		// Submit access to the back office to the ROLE_AUTHOR role
		'security.role_hierarchy' => array(
				'ROLE_AUTHOR' => array('ROLE_USER'),
		),
		'security.access_rules' => array(
				array('^/author', 'ROLE_AUTHOR'),
		),
		// Submit access to the back office to the ROLE_MODERATOR role
		'security.role_hierarchy' => array(
				'ROLE_MODERATOR' => array('ROLE_USER'),
		),
		'security.access_rules' => array(
				array('^/moderator', 'ROLE_MODERATOR'),
		),
		
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());

// Register services.
$app['dao.post'] = function($app) {
	return new BlogEcrivain\DAO\PostDAO($app['db']);
};

$app['dao.user'] = function($app) {
	return new BlogEcrivain\DAO\UserDAO($app['db']);
};

//Inject dependence on the PostDAO class to the CommentDAO instance through the setPostDAO mutator.
$app['dao.comment'] = function($app) {
	$commentDAO = new BlogEcrivain\DAO\CommentDAO($app['db']);
	$commentDAO->setPostDAO($app['dao.post']);
	$commentDAO->setUserDAO($app['dao.user']);
	return $commentDAO;
};