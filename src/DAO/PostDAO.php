<?php 

namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Post;

class PostDAO extends DAO {
	
	/**
	 * Return a list of all posts sorted by most recent
	 * 
	 * @return array A list of all posts
	 */
	public function recoverAllPost() {
		$req = "SELECT * FROM posts ORDER BY id_post DESC";
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
	 * Return a post matching th supplied id
	 * 
	 * @param integer $id
	 * 
	 * @return \BlogEcrivain\Domain\Post|throw an exception if no matching post is found
	 */
	public function recoverPost($id) {
		$req = "SELECT * FROM posts WHERE id_post=?";
		$row = $this->getDb()->fetchAssoc($req, array($id));
		if($row)
			return $this->buildDomainObject($row);
		else 
			throw new \Exception("No post matching id" . $id);
	}
	
	
	/**
	 * Creat an Post object based on a database row
	 * 
	 * @param array $row The database row containing Post data
	 * @return \blog_ecrivain\Domain\Post
	 */
	protected function buildDomainObject(array $row) {
		
		$post = new Post();
		$post->setId($row['id_post']);
		$post->setTitle($row['title']);
		$post->setDate($row['p_date']);
		$post->setContent($row['content']);
		return $post;
	}
}