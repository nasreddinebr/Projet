<?php
use Symfony\Component\HttpFoundation\Request;
use BlogEcrivain\Domain\Comment;
use BlogEcrivain\Domain\Post;
use BlogEcrivain\Domain\User;
use BlogEcrivain\Domain\Media;
use BlogEcrivain\Form\Type\CommentWrite;
use BlogEcrivain\Form\Type\PostWrite;
use BlogEcrivain\Form\Type\UserAdminWrite;
use BlogEcrivain\Form\Type\UserSignUp;
use BlogEcrivain\bin\StopReturn;


/**************************************************************************************/
/**									  Front-Office				                 	 **/
/**************************************************************************************/

// Home page
$app->get('/', function() use ($app) {
	$posts = $app['dao.post']->recoverAllPostPublished();
	return $app['twig']->render('index.html.twig', array('posts' => $posts));
})->bind('home');

// Post details and comments
$app->match('/post/{id}', function ($id, Request $req) use ($app){
	// Recuperate a post via its id
	$post = $app['dao.post']->recoverPostPublished($id);
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
			$app['session']->getFlashBag()->add('success', 'Votre commentaire a été enregistré avec succès.');
			
			// Stop the repetitive return of a form
			$stopReturn = new StopReturn();
			
		}
		$commentFormView = $commentForm->createView();
	}
	$comments = $app['dao.comment']->recoverAllCommentByPost($id);

	//var_dump($comments);
	return $app['twig']->render('post.html.twig', array(
			'post' 			=> $post, 
			'comments' 		=> $comments,
			'commentForm' 	=> $commentFormView));
})->bind('post');

// Blog page
$app->get('/blog', function() use ($app) {
	$listPosts = $app['dao.post']->recoverAllPostPublished();
	return $app['twig']->render('blog.html.twig', array('posts' => $listPosts));
})->bind('blog');

/**************************************************************************************/
/**							Authentication and Back-Office							 **/
/**************************************************************************************/

// Sign up
$app->get('/signup', function(Request $request) use ($app) {
	return $app['twig']->render('sign_up.html.twig');
})->bind('signup');

//Login page
$app->get('/login', function(Request $request) use ($app) {
	return $app['twig']->render('login.html.twig', array(
			'error' 		=> $app['security.last_error']($request),
			'last_username' => $app['session']->get('_security.last_username'),	
	));
})->bind('login');

// Admin page
$app->get('/admin', function (Request $request) use ($app) {
	$posts = $app['dao.post']->recoverAllPost();
	return $app['twig']->render('admin.html.twig', array('posts' => $posts));
})->bind('admin');

//author page
$app->get('/admin/author', function (Request $request) use ($app) {
	$posts = $app['dao.post']->recoverAllPost();
	return $app['twig']->render('admin.html.twig', array('posts' => $posts));	
})->bind('author');

// Users page
$app->get('/admin/users', function (Request $request) use ($app) {
	$users = $app['dao.user']->recoverAllUsers();
	return $app['twig']->render('users_list.html.twig', array('users' => $users));
})->bind('user');

// Moderator page
$app->get('/admin/moderator', function (Request $request) use ($app) {
	$comments = $app['dao.comment']->recoverUnreadComment();
	return $app['twig']->render('comments.html.twig', array('comments' => $comments));
})->bind('moderator');

/**************************************************************************************/
/**									Posts Management							     **/
/**************************************************************************************/

// Add a new post
$app->match('/admin/post/add', function (Request $request) use ($app) {
	// We check if there is a user logged in
	if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
		
		// The author aor admin is identified, recuperate it.
		$post = new Post();
		$media = new Media();
		$user = $app['user'];
		$post->setAuthor($user);
		
		// Creation of a new post and the form to associate it
		$postForm = $app['form.factory']->create(PostWrite::class, $post);
		$postForm->handleRequest($request);
		
		/**
		 * If a post is submitted and the content is valid,
		 * the new post is saved and a message of success is displayed.
		 */
		if ($postForm->isSubmitted() && $postForm->isValid()) {
			
			// verifying whether the uploader file extension is allowed or not
			$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
			$extension_upload = strtolower(substr(strrchr($_FILES['post_write']['name']['media'], '.'), 1));
			if (in_array($extension_upload, $extensions_valides)){
				$app['dao.post']->addPost($post);
				$id_post = $post->getId();		// Recuperate the id of last post
				
				//Recover the temporary path and the destination folder, and then rename the uploder file
				$tmp_name = $_FILES['post_write']['tmp_name']['media'];
				$upload_dir = '../web/img/PostsMedia';		
				$fileName = "background{$id_post}.{$extension_upload}";
				
				//if the folde does not exit, we create it with a writing mode.
				if (!file_exists($upload_dir)) {
					mkdir($upload_dir, 0777);
				}
				
				// Upload the file by changing he's name "background(idPost).extention_upload"
				move_uploaded_file($tmp_name, "$upload_dir/$fileName");
				$media->setFileName($fileName);
				$media->setUrlFile(substr($upload_dir, -15));
				$media->setPostId($id_post);
				
				// Add media to the DataBase
				$app['dao.media']->addMedia($media);
				
				$app['session']->getFlashBag()->add('success','Le billet a été enregistré avec succès.');
				
				// Stop the repetitive return of a form
				$stopReturn = new StopReturn();
				
			}else {
				$app['session']->getFlashBag()->add('error','L\'extension du fichier est invalide veuillez sélectionner une image valide.');
			}
			
		}
	}
	return $app['twig']->render('post_form.html.twig', array(
			'title' 	=> 'Nouveau Billet',
			'postForm' 	=> $postForm->createView()
	));
})->bind('admin_article_add');

// Edit an existing post
$app->match('/admin/post/{id}/edit', function ($id, Request $request) use ($app) {
	$post = $app['dao.post']->recoverPostEdit($id);
	$postForm = $app['form.factory']->create(PostWrite::class, $post);
	$postForm->handleRequest($request);
	if ($postForm->isSubmitted() && $postForm->isValid()) {
		$app['dao.post']->addPost($post);
		$app['session']->getFlashBag()->add('success', 'Le billet a été mis à jour avec succès.');
	}
	return $app['twig']->render('post_form.html.twig', array(
			'title' => 'Edit post',
			'postForm'	=> $postForm->createView()
	));
})->bind('admin_post_edit');

// Delete a post and it's comments
$app->get('/admin/post/{id}/delete', function ($id, Request $request) use ($app) {

	// Delete all associated comments
	$app['dao.comment']->deletAllCommentByPost($id);
	
	// Delete all associated media.
	$app['dao.media']->removeMediaByPsot($id);
	
	// Delete the post
	$app['dao.post']->deletPost($id);
	$app['session']->getFlashBag()->add('success', 'Le billet a été suprimé avec succès.');
	
	//Redirecte to admin home page
	return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_post_remove');

/**************************************************************************************/
/**									Comments Management							     **/
/**************************************************************************************/

// Update comment to read
$app->get('/admin/comment/{id}/read', function ($id, Request $request) use ($app) {
	
	// Read a comment by ID
	$app['dao.comment']->readComment($id);
	$app['session']->getFlashBag()->add('success', 'Le commentaire a été marqué comme lu.');
	
	// Redirect to admin home page
	return $app->redirect($app['url_generator']->generate('moderator'));
	
})->bind('admin_Comment_read');

//Report a comment
$app->get('/post/{postid}/comment/{id}', function ($postid, $id, Request $request) use ($app) {

	//Report a comment
	$app['dao.comment']->reportComment($id);
	$app['session']->getFlashBag()->add('success', 'Le commentaire à bien été signalé');
	return $app->redirect($app['url_generator']->generate('post', array('id' => $postid)));
})->bind('report_comment');

// Remove a comment
$app->get('/admin/comment/{id}/delete', function ($id, Request $request) use ($app){
	// Delete a comment by ID
	$app['dao.comment']->removeComment($id);
	$app['session']->getFlashBag()->add('success', 'Le commentaire a été supprimé avec succès.');
	
	// Redirect to admin home page
	return $app->redirect($app['url_generator']->generate('moderator'));
})->bind('admin_comment_delete');

/**************************************************************************************/
/**									Users Management							     **/
/**************************************************************************************/

// Admin add user if not exist
$app->match('/admin/user/add', function (Request $request) use ($app) {
	$user = new User();
	
	$userForm = $app['form.factory']->create(UserAdminWrite::class, $user);
	$userForm->handleRequest($request);
	if ($userForm->isSubmitted() && $userForm->isValid()) {
		$salt = substr(sha1(time()), 0, 23);
		$user->setSalt($salt);
		$userPassword = $user->getPassword();
		
		// Get  the default encoder
		$encoder = $app['security.encoder.bcrypt'];
		
		// Compute the encoded password
		$password = $encoder->encodePassword($userPassword, $user->getSalt());
		$user->setPassword($password);
		
		//Check the existence of username and email
		$userExist = $app['dao.user']->userExists($user);
		$emailExist = array_merge($userExist, $app['dao.user']->emailExists($user)) ;
		if ($userExist['username_exist']) {
			$app['session']->getFlashBag()->add('error', 'Le nom d\'utilisateur choisit existe déja, veuillez le modifier');
		}else {
			if ($emailExist['email_exist']) {
				$app['session']->getFlashBag()->add('error', 'Cette adresse E-mail existe déja, veuillez en saisir une autre');
			}else {
				// Save the user if the username or the email did not already exist
				$app['dao.user']->addUser($user);
				$app['session']->getFlashBag()->add('success', 'l\'utilisateur à bien été enregistré.');
				
				// Stop the repetitive return of a form
				$stopReturn = new StopReturn();
			}
		}
	}
	return $app['twig']->render('user_admin_form.html.twig', array(
			'title'		=> 'Nouveau utilisateur',
			'userForm'	=> $userForm->createView()));
	
})->bind('admin_user_add');

// Edit an existing user
$app->match('admin/user/{id}/edit', function ($id, Request $request) use ($app) {
	$user = $app['dao.user']->recoverUserById($id);
	$userForm = $app['form.factory']->create(UserAdminWrite::class, $user);
	$userForm ->handleRequest($request);
	if ($userForm->isSubmitted() && $userForm->isValid()) {
		$userPassword = $user->getPassword();
		
		//get the encoder
		$encoder = $app['security.encoder_factory']->getEncoder($user);
		
		// hash the password
		$password = $encoder->encodePassword($userPassword, $user->getSalt());
		$user->setPassword($password);
		$app['dao.user']->addUser($user);
		$app['session']->getFlashBag()->add('success', 'L\'utilisateur a été mis à jour avec succès.');
	}
	return $app['twig']->render('user_admin_form.html.twig', array(
			'title'		=> 'Edit user',
			'userForm'	=> $userForm->createView()));
})->bind('admin_user_edit');

// Remove a user
$app->match('admin/user/{id}/delete', function ($id, Request $request) use ($app) {
	// Delete all associated comments
	$app['dao.comment']->deletAllCommentByUser($id);
	// Delete all associated Posts
	$app['dao.post']->deletAllPostByUser($id);	
	// Delete a user
	$app['dao.user']->removeUser($id);
	$app['session']->getFlashBag()->add('success', 'L\'utilisateur a été supprimé avec succès.');
	
	// Redirect to admin home page
	return $app->redirect($app['url_generator']->generate('user'));
})->bind('admin_user_delete');

// User registration
$app->match('/signup/user', function (Request $request) use ($app) {
	$user = new User();
	$userForm = $app['form.factory']->create(UserSignUp::class, $user);
	$userForm->handleRequest($request);
	if ($userForm->isSubmitted() && $userForm->isValid()) {
		
		// Generate a random salt value
		$salt = substr(sha1(time()), 0, 23);
		$user->setSalt($salt);
		$userPassword = $user->getPassword();
		
		// get  the default encoder
		$encoder = $app['security.encoder.bcrypt'];
		
		// Compute the encoded password
		$password = $encoder->encodePassword($userPassword, $user->getSalt());
		$user->setPassword($password);
		$user->setRole('ROLE_USER');
		
		//Check the existence of username and emails
		$userExist = $app['dao.user']->userExists($user);
		$userExist = array_merge($userExist, $app['dao.user']->emailExists($user)) ;
		if ($userExist['username_exist']) {
			$app['session']->getFlashBag()->add('error', 'Le nom d\'utilisateur choisit existe déja, veuillez le modifier');
		}else {
			if ($userExist['email_exist']) {
				$app['session']->getFlashBag()->add('error', 'Cette adresse E-mail existe déja, veuillez en saisir une autre');
			}else {
				//Save the user if the username or the email did not already exist
				$app['dao.user']->addUser($user);
				$app['session']->getFlashBag()->add('success', 'Votre inscription a été effectué avec succès.');
			}
		}
		
	}
	return $app['twig']->render('sign_up.html.twig', array(
			'title'		=> 'Nouveau utilisateur',
			'userForm'	=> $userForm->createView()));
})->bind('user_add');