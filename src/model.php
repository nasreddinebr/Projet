<?php
// Return all articles
function getPosts() {
	// Data access
	$db= new PDO('mysql:host=localhost;dbname=blog_ecrivain;charset=utf8', 'nasre', 'gBc546br35*lm');
	
	$posts = $db->query('select * from post order by id_post desc');
	return $posts;
}