<?php
namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Comment;
use Doctrine\DBAL\Driver\SQLSrv\LastInsertId;

class CommentDAO extends DAO {
	
	/**
	 * @var \BlogEcrivain\DAO\PostDAO
	 */
	private $postDAO;
	
	/**
	 * @var \BlogEcrivain\DAO\UserDAO
	 */
	private $userDAO;
	
	public function setPostDAO(PostDAO $postDAO) {
		$this->postDAO = $postDAO;
	}
	
	public function setUserDAO(UserDAO $userDAO) {
		$this->userDAO = $userDAO;
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
		
		/* id_post is not selected by the SQL query.
		 The post won't be retrieved during domain object construction.*/
		$req = "SELECT id_comment, date_comment, content, user_id FROM comments WHERE post_id=? ORDER BY id_comment";
		$response = $this->getDb()->fetchAll($req, array($postId));
		
		//Convert Query response to an array of domain objects
		$comments = array();
		foreach ($response as $row) {
			$commentId = $row['id_comment'];
			$comment = $this->buildDomainObject($row);
			
			//The associated post is defined for the constructed comment
			$comment->setPost($post);
			$comments[$commentId] = $comment;
		}
		if(isset($comments)) {
			return $comments;
		}
					
	}
	
	/**
	 * Add a comment to the database
	 * 
	 * @param \BlogEcrivain\Domain\Comment
	 */
	public function addComment(Comment $comment) {
		$commentData = array(
				'content' 		=> $comment->getContent(),
				//'report'		=> $comment->getReport(),
				//'read'			=> $comment->getRead(),
				'post_id' 		=> $comment->getPost()->getId(),
				'user_id' 		=> $comment->getAuthor()->getId()
		);
		if ($comment->getId()) {
			//The comment has been added: update it
			$this->getDb()->update('comments', $commentData, array('id_comment' => $comment->getId()));
		}else {
			//The comment has not been added: insert it
			$this->getDb()->insert('comments', $commentData);
			
			// Recuperate the id of the new comment then insert it on the entity
			$id = $this->getDb()->lastInsertId();
			$comment->setId($id);
		}
	}
	
	/**
	 * Recover a list of all comment unread
	 *
	 * @return array
	 */
	
	public function recoverUnreadComment() {
		
		$req = "SELECT * FROM comments WHERE read_comment=0 ORDER BY date_comment ASC";
		$response = $this->getDb()->fetchAll($req);
		
		// Convert query result to a array of domain objects
		$posts = array();
		foreach ($response as $row) {
			$comentId = $row['id_comment'];
			$comments[$comentId] = $this->buildDomainObject($row);
			
		}
		
		return $comments;
	}
	
	/**
	 * Return a comment matching the suplied id
	 * 
	 * @param integer $id
	 */
	public function recoverComment($id) {
		$req = "SELECT * FROM comments WHERE id_comment=?";
		$response = $this->getDb()->fetchAssoc($req, array($id));
		if ($response){
			return $this->buildDomainObject($response);
		}else {
			throw new \Exception("Aucun commentaire ne correspond à l'id " . $id);
		}
	}
	/**
	 * Removes a comment from the data base
	 * 
	 * @param integer $id
	 */
	public function delete($id) {
		// Delete the comment
		$this->getDb()-delete('comments', array('id_comment' => $id));
	}
	
	
	/**
	 * Delete all comments for a post
	 * 
	 * @param $postId
	 */
	public function deletAllCommentByPost($postId) {
		$this->getDb()->delete('comments', array('post_id' => $postId));
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
		//$comment->setReport($row['report']);
		//$comment->setRead($row['read_comment']);
		
		if (array_key_exists('post_id', $row)) {
			// Find and set the associated post
			$postId = $row['post_id'];
			$post = $this->postDAO->recoverPost($postId);
			$comment->setPost($post);
		}
		if (array_key_exists('user_id', $row)) {
			//recuperate and set the associated author
			$userId = $row['user_id'];
			$user = $this->userDAO->recoverUserById($userId);
			$comment->setAuthor($user);
		}
		return $comment;
	}	
}