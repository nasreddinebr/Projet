<?php 

namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Post;
use Doctrine\DBAL\Driver\SQLSrv\LastInsertId;
use BlogEcrivain\Domain\Media;

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
		$req = "SELECT * FROM posts p INNER JOIN medias m ON m.post_id = p.id_post ORDER BY p.id_post DESC";
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
	 * Return a post matching th supplied id and published
	 * 
	 * @param integer $id
	 * 
	 * @return \BlogEcrivain\Domain\Post|throw an exception if no matching post is found
	 */
	public function recoverPostPublished($id) {
		$req = "SELECT * FROM posts p INNER JOIN medias m ON m.post_id = p.id_post WHERE id_post=? AND publish=1";
		$row = $this->getDb()->fetchAssoc($req, array($id));
		if($row)
			return $this->buildDomainObject($row);
		else 
			throw new \Exception("Aucun billet ne correspond à l'identifiant: " . $id);
	}
	
	/**
	 * Return a post matching th supplied id for editing
	 *
	 * @param integer $id
	 *
	 * @return \BlogEcrivain\Domain\Post|throw an exception if no matching post is found
	 */
	public function recoverPostEdit($id) {
		$req = "SELECT * FROM posts p INNER JOIN medias m ON m.post_id = p.id_post WHERE id_post=?";
		$row = $this->getDb()->fetchAssoc($req, array($id));
		if($row)
			return $this->buildDomainObject($row);
			else
				throw new \Exception("Aucun billet ne correspond à l'identifiant: " . $id);
	}
	
	/**
	 * Returns the list of posts that are published
	 *
	 * @return array A list of all posts
	 */
	public function recoverAllPostPublished() {
		$req = "SELECT * FROM posts p INNER JOIN medias m ON m.post_id = p.id_post WHERE publish=1 ORDER BY id_post DESC";
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
	 * Delete all post for a user
	 *
	 * @param integer $user_id
	 */
	public function deletAllPostByUser($userId) {
		$this->getDb()->delete('posts', array('user_id' => $userId));
	}
	
	/**
	 * Creat an Post object based on a database row
	 * 
	 * @param array $row The database row containing Post data
	 * @return \blog_ecrivain\Domain\Post
	 */
	protected function buildDomainObject(array $row) {
		$media = new Media();
		$post = new Post();
		$post->setId($row['id_post']);
		$post->setTitle($row['title']);
		$post->setDate($row['p_date']);
		$post->setContent($row['content']);
		$media= array($row['file_name'], $row['url_file']);
		$post->setMedia($media);
		if (array_key_exists('user_id', $row)) {
			
			//recuperate and set the associated author
			$userId = $row['user_id'];
			$user = $this->userDAO->recoverUserById($userId);
			$post->setAuthor($user);
		}
		return $post;
	}
}