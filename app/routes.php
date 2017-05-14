<?php
// Home page
$app->get('/', function() use ($app) {
	$posts = $app['dao.post']->recoverAllPost();
	
	return $app['twig']->render('index.html.twig', array('posts' => $posts));
});

