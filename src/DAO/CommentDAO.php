<?php
namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Comment;

class CommentDAO extends DAO {
	
	/**
	 * @var \BlogEcrivain\DAO\PostDAO
	 */
	private $postDAO;
	
	public function setPostDAO(PostDAO $postDAO) {
		$this->postDAO = $postDAO;
	}
	
	/**
	 * Return all comments for a post, order by must recent.
	 * 
	 * @param integer $postId
	 * 
	 * Return array with all comments for a post
	 */
	public function  recoverAllCommentByPost($postId) {
		// The associated post is retrieved only once
		$post = $this->postDAO->recoverPost($postId);
		
		// id_post is not selected by the SQL query
		// The post won't be retrieved during domain object construction
		$req = "SELECT id_comment, date_comment, content FROM comments WHERE post_id=? ORDER BY id_comment";
		$response = $this->getDb()->fetchAll($req, array($postId));
		
		//Convert Query response to an array of domain objects
		foreach ($response as $row) {
			$commentId = $row['id_comment'];
			$comentDate = $row['date_comment'];
			$comment = $this->buildDomainObject($row);
			
			//The associated post is defined for the constructed comment
			$comment->setPost($post);
			$comments[$commentId] = $comment;
		}
		return $comments;		
	}
	
	/**
	 * Creat an commengt object based on a DB row
	 * 
	 * @param array $row The DB row containing Coment data
	 *  
	 * @return \BlogEcrivain\Domain\Comment
	 */
	protected  function buildDomainObject(array $row) {
		$comment = new Comment();
		$comment->setId($row['id_comment']);
		$comment->setDateComment($row['date_comment']);
		$comment->setContent($row['content']);
		
		if (array_key_exists('id_post', $row)) {
			// Find and set the associated post
			$postId = $row['id_post'];
			$post = $this->postDAO->recoverPost($postId);
			$comment->setPost($post);
		}
		return $comment;
	}
}