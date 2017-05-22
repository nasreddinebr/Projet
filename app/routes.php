<?php
use Symfony\Component\HttpFoundation\Request;
use BlogEcrivain\Domain\Comment;
use BlogEcrivain\Form\Type\CommentWrite;

// Home page
$app->get('/', function() use ($app) {
	$posts = find($app);
	return $app['twig']->render('index.html.twig', array('posts' => $posts));
})->bind('home');

// Post detils and comments
$app->match('/post/{id}', function ($id, Request $req) use ($app){
	// Recuperate a post via its id
	$post = $app['dao.post']->recoverPost($id);
	$commentFormView = null;
	
	// We check if there is a user logged in
	if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
		// The user is identified, recuperate it.
		$comment = new Comment();
		$comment->setPost($post);
		$user = $app['user'];
		$comment->setAuthor($user);
		
		// Creation of a new comment and the form to associate it
		$commentForm = $app['form.factory']->create(CommentWrite::class, $comment);
		$commentForm->handleRequest($req);
		
		/*
		 * If the comment is submitted and the content is valid, 
		 * the new comment is saved and a message of success is displayed.
		 */
		if ($commentForm->isSubmitted() && $commentForm->isValid()) {
			$app['dao.comment']->addComment($comment);
			$app['session']->getFlashBag()->add('success', 'Votre commentaire et enregistrer.');
		}
		$commentFormView = $commentForm->createView();
	}
	
	$comments = $app['dao.comment']->recoverAllCommentByPost($id);
	return $app['twig']->render('post.html.twig', array(
			'post' 			=> $post, 
			'comments' 		=> $comments,
			'commentForm' 	=> $commentFormView));
})->bind('post');

// Blog page
$app->get('/blog', function() use ($app) {
	$listPosts = find($app);
	return $app['twig']->render('blog.html.twig', array('posts' => $listPosts));
})->bind('blog');

//Login page
$app->get('/signin', function(Request $request) use ($app) {
	return $app['twig']->render('login.html.twig', array(
			'error' => $app['security.last_error']($request),
			'last_username' => $app['session']->get('_security.last_username'),	
	));
	
})->bind('signin');