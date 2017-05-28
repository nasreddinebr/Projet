<?php 

namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Post;
use Doctrine\DBAL\Driver\SQLSrv\LastInsertId;

class PostDAO extends DAO {
	
	/**
	 * @var \BlogEcrivain\DAO\UserDAO
	 */
	private $userDAO;
	
	public function setUserDAO(UserDAO $userDAO) {
		$this->userDAO = $userDAO;
	}
	
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
	 * Add a new post into th DB
	 * 
	 * @param \BlogEcrivain\Domain\Post $post The post to add 
	 */
	public function addPost(Post $post) {
		$postData = array(
				'title' 	=> $post->getTitle(),
				'content' 	=> $post->getContent(),
				'publish'	=> $post->getPublish(),
				'user_id' 	=> $post->getAuthor()->getId()
		);
		if ($post->getId()) {
			
			// The post already exists: update it
			$this->getDb()->update('posts', $postData, array('id_post' => $post->getId()));
		}else {
			// The post does not exist: insert it.
			$this->getDb()->insert('posts', $postData);
			
			// Get the id of new post and set it on the entity
			$id = $this->getDb()->lastInsertId();
			$post->setId($id);
		}
	}
	
	/**
	 * Delete a post from the database
	 * 
	 * @param integer $id 
	 */
	public function deletPost($id) {
		// Delete the post
		$this->getDb()->delete('posts', array('id_post' => $id));
	}
	
	/**
	 * Returns the list of posts that are published
	 * 
	 * @return array A list of all posts
	 */
	public function recoverPostPublished() {
		$req = "SELECT * FROM posts WHERE publish=1 ORDER BY id_post DESC";
		$response = $this->getDb()->fetchAll($req);
		
		// Convert query result to a array of domain objects
		$posts = array();
		foreach ($response as $row) {
			$postId = $row['id_post'];
			$posts[$postId] = $this->buildDomainObject($row);
		}
		if (isset($posts)) {
			return $posts;
		}	
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
		if (array_key_exists('user_id', $row)) {
			//recuperate and set the associated author
			$userId = $row['user_id'];
			$user = $this->userDAO->recoverUserById($userId);
			$post->setAuthor($user);
		}
		return $post;
	}
}