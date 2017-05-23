<?php
function findPosts($app) {
	$findAllPosts = $app['dao.post']->recoverAllPost();
	return $findAllPosts;
}



