<?php
// Home page
$app->get('/', function() {
	require '../src/model.php';
	$posts = getPosts();
	ob_start();		//start buffering HTML output
	require '../views/view.php';
	$view = ob_get_clean();		//Assign HTML output to $view
	return $view;
});

