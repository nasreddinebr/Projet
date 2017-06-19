<?php
namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Comment;
use Doctrine\DBAL\Driver\SQLSrv\LastInsertId;

class CommentDAO extends DAO {
	
	/**
	 * @param array of object
	 */
	//private $comment_by_id;
	
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
		$post = $this->postDAO->recoverPostPublished($postId);
		
		/* id_post is not selected by the SQL query.
		 The post won't be retrieved during domain object construction.*/
		$req = "SELECT * FROM comments WHERE post_id=? ORDER BY id_comment";
		$response = $this->getDb()->fetchAll($req, array($postId));
		
		//Convert Query response to an array of domain objects
		
		foreach ($response as $row) {
			$commentId = $row['id_comment'];
			$comment = $this->buildDomainObject($row);
			
			//The associated post is defined for the constructed comment
			$comment->setPost($post);
			$comments[$commentId] = $comment;
		}
		
		//Reorganize the comments with their reply.
		$commentById = [];
		if (isset($comments)) {
			foreach ($comments as $comment) {
				$commentById[$comment->id]=$comment;
			}
			
			foreach ($comments as $key => $comment) {
				if ($comment->parent_id != 0) {
					$commentById[$comment->parent_id]->children[] = $comment;
					unset($comments[$key]);
				}
			}
			
			//Initializes comment_by_id to use it later.
			//$this->comment_by_id = $comments;
			return $comments;
		}		 					
	}
	
	/**
	 * Add a comment to the database or update it
	 * 
	 * @param \BlogEcrivain\Domain\Comment
	 */
	public function addComment(Comment $comment) {	//$parentId
		$commentData = array(
				'content' 		=> $comment->getContent(),
				'parent_id'		=> $comment->getParentId(),
				'depth'			=> $comment->getDepth(),
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
		
		$req = "SELECT * FROM comments WHERE read_comment=0 ORDER BY report DESC";
		$response = $this->getDb()->fetchAll($req);
		
		// Convert query result to a array of domain objects
		$posts = array();
		foreach ($response as $row) {
			$comentId = $row['id_comment'];
			$comments[$comentId] = $this->buildDomainObject($row);
		}
		if (isset($comments)) return $comments;
	}
	
	/**
	 * 
	 * Mark a comment as read
	 * 
	 * @param integer $id comment id
	 */
	public function readComment($id) {
		$see = 1;
		$commentData = array('read_comment'	=> $see);
		if ($id) {
			
			//The comment has been added: update it
			$this->getDb()->update('comments', $commentData, array('id_comment' => $id));
		}else {
			throw new \Exception("Aucun commentaire ne correspond à l'id " . $id);
		}
	}
	
	/**
	 * Report a comment
	 * 
	 * @param integer $id comment id
	 */
	public function reportComment($id) {
		$report = 1;
		$commentReport = array('report' => 1);
		if ($id) {
			$this->getDb()->update('comments', $commentReport, array('id_comment' => $id));
		} else {
			throw new \Exception("Aucun commentaire ne correspond à l'id " . $id);
		}
	}
	
	/**
	 * Delete a comment with it's children
	 * 
	 * @param integer $id
	 */
	public function removeComment($id) {
		//var_dump($id);
		$req = "SELECT * FROM comments WHERE id_comment=?";
		$response = $this->getDb()->fetchAssoc($req,array($id));
		//var_dump($response);
		
		if ($response['parent_id'] == 0){
			//recuperate all comment by post_id
			$comments = $this->recoverAllCommentByPost($response['post_id']);
			
			// Get the list of the ids of the comment to delete and it is children
			$ids = $this->getChildrenIds($comments[$response['id_comment']]);
		}/*else {
			//recuperate all comment by post_id
			$comments = $this->recoverAllCommentByPost($response['post_id']);
			var_dump($comments[$response['parent_id']]);
			
			// Get the list of the ids of the comment to delete and it is children
			$ids = $this->getChildrenIds($comments[]->$response['id_comment']);
			var_dump($ids);
			die();
		}
		$ids[] = $response['id_comment'];
		var_dump($ids);*/
		
		// Delete the comment and he's reply
		$this->getDb()->exec('DELETE FROM comments WHERE id_comment IN (' . implode(',', $ids) . ')');
	}
	
	// Obtain a table that will contain the id list of any child comment
	public function getChildrenIds($comments){
		if (isset($comments->children)){
			$ids=[];
			foreach ($comments->children as $child) {
				$ids[] = $child->id;
				
				// Check whether the child in question has children
				if (isset($child->children)) {
					/**
					 *  Recursion(La récursivité: fonction qui s'appelle elle-même)
					 * 	and Merge the two array
					 */
					$ids = array_merge($ids,$this->getChildrenIds($child));
				}
			}
			return $ids;
		}	
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
	 * Delete all comments for a user
	 * 
	 * @param integer $user_id
	 */
	public function deletAllCommentByUser($userId) {
		$this->getDb()->delete('comments', array('user_id' => $userId));
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
		$comment->setParentId($row['parent_id']);
		$comment->setDepth($row['depth']);
		
		if (array_key_exists('post_id', $row)) {
			// Find and set the associated post
			$postId = $row['post_id'];
			$post = $this->postDAO->recoverPostPublished($postId);
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