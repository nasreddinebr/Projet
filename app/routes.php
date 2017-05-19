<?php
use Symfony\Component\HttpFoundation\Request;

// Home page
$app->get('/', function() use ($app) {
	$posts = find($app);
	return $app['twig']->render('index.html.twig', array('posts' => $posts));
})->bind('home');

// Post detils and comments
$app->get('/post/{id}', function ($id) use ($app){
	$post = $app['dao.post']->recoverPost($id);
	$comments = $app['dao.comment']->recoverAllCommentByPost($id);
	return $app['twig']->render('post.html.twig', array(
			'post' => $post, 
			'comments' => $comments));
})->bind('post');

// Blog page
$app->get('/blog', function() use ($app) {
	$listPosts = find($app);
	return $app['twig']->render('blog.html.twig', array('posts' => $listPosts));
})->bind('blog');

//Login page
$app->get('/signin', function(Request $request) use ($app) {
	return $app['twig']->render('signin.html.twig', array(
			'error' => $app['security.last_error']($request),
			'last_username' => $app['session']->get('_security.last_username'),	
	));
	
})->bind('signin');