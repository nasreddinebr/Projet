<?php
function find($app) {
	$findAllPosts = $app['dao.post']->recoverAllPost();
	return $findAllPosts;
}