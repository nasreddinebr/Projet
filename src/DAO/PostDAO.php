<?php 

namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Post;

class PostDAO extends DAO {
	
	/**
	 * Returne a list of all posts sorted by most recent
	 * 
	 * @return array A list of all posts
	 */
	public function recoverAllPost() {
		$req = "select * from posts order by id_post desc";
		$response = $this->getDb()->fetchAll($req);
		
		// Convert query result to a array of domain objects
		$posts = array();
		foreach ($response as $row) {
			$postId = $row['id_post'];
			$posts[$postId] = $this->buildDomainObject($row);
		}
		return $posts;
	}
	
	
	/**
	 * Creat an Post object based on a database row
	 * 
	 * @param array $row The database row containing Post data
	 * @return \blog_ecrivain\Domain\Post
	 */
	protected function buildDomainObject($row) {
		
		$post = new Post();
		$post->setId($row['id_post']);
		$post->setTitle($row['title']);
		$post->setDate($row['p_date']);
		$post->setContent($row['content']);
		return $post;
	}
}