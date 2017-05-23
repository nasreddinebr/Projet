<?php
use Symfony\Component\HttpFoundation\Request;
use BlogEcrivain\Domain\Comment;
use BlogEcrivain\Domain\Post;
use BlogEcrivain\Form\Type\CommentWrite;
use BlogEcrivain\Form\Type\PostWrite;

// Home page
$app->get('/', function() use ($app) {
	$posts = findPosts($app);
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
	$listPosts = findPosts($app);
	return $app['twig']->render('blog.html.twig', array('posts' => $listPosts));
})->bind('blog');

//Login page
$app->get('/signin', function(Request $request) use ($app) {
	return $app['twig']->render('login.html.twig', array(
			'error' 		=> $app['security.last_error']($request),
			'last_username' => $app['session']->get('_security.last_username'),	
	));
	
})->bind('signin');

// Admin page
$app->get('/admin', function () use ($app) {
	$posts = findPosts($app);
	$comments = $app['dao.comment']->recoverAllComments();
	$users = $app['dao.user']->recoverAllUsers();
	return $app['twig']->render('admin.html.twig', array(
		'posts' 	=> $posts,
		'comments' 	=> $comments,
		'users'		=> $users
	));
})->bind('admin');

// Author page
$app->get('/author', function () use ($app) {
	$posts = findPosts($app);
	$comments = $app['dao.comment']->recoverAllComments();
	return $app['twig']->render('author.html.twig', array(
			'posts' 	=> $posts,
			'comments' 	=> $comments
	));
})->bind('author');

// Moderator page
$app->get('/moderator', function () use ($app) {
	$comments = $app['dao.comment']->recoverAllComments();
	return $app['twig']->render('moderator.html.twig', array('comments' => $comments));
})->bind('moderator');

// Add a new post
$app->match('/admin/post/add', function (Request $request) use ($app) {
	$post = new Post();
	$postForm = $app['form.factory']->create(PostWrite::class, $post);
	$postForm->handleRequest($request);
	if ($postForm->isSubmitted() && $postForm->isValid()) {
		$app['dao.post']->addPost($post);
		$app['session']->getFlasBag()->add('seccess','Le billet et enregistrer.');
	}
	return $app['twig']->render('post_form.html.twig', array(
			'title' 	=> 'Nouveau Billet',
			'postForm' 	=> $postForm->createView()
	));
})->bind('admin_article_add');

// Edit an existing post


// Delete a post and it's comments

