<?php 

namespace BlogEcrivain\DAO;
use Doctrine\DBAL\Connection;
use BlogEcrivain\Domain\Post;

class PostDAO {
	
	/**
	 * Database connection
	 *
	 * @var \Doctrine\DBAL\Connection
	 */
	private $db;
	
	/**
	 * Constructor
	 * 
	 * @param \Doctrine\DBAL\Connection The database connection object
	 */
	public function __construct(Connection $db){
		$this->db = $db;
	}
	
	/**
	 * Returne a list of all posts
	 * 
	 * @return array A list of all posts
	 */
	public function recoverAllPost() {
		$req = "select * from posts order by id_post desc";
		$response= $this->db->fetchAll($req);
		
		// Convert query result to a array of domain objects
		$posts = array();
		foreach ($response as $row) {
			$postId = $row['id_post'];
			$posts[$postId] = $this->buildPost($row);
		}
		return $posts;
	}
	
	
	/**
	 * Creat an Post object based on a database row
	 * 
	 * @param array $row The database row containing Post data
	 * @return \blog_ecrivain\Domain\Post
	 */
	private function buildPost(array $row) {
		$post = new Post();
		$post->setId($row['id_post']);
		$post->setTitle($row['title']);
		$post->setDate($row['p_date']);
		$post->setContent($row['content']);
		return $post;
	}
}