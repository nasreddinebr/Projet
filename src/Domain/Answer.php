<?php

class Answer {
	
	/**
	 * Answer id
	 *
	 * @var integer
	 * @access private
	 */
	private  $id;
	
	/**
	 * Date answer
	 *
	 * @var DateTime
	 * @access private
	 */
	private  $date;
	
	/**
	 * Answer content
	 *
	 * @var string
	 * @access private
	 */
	private  $content;
	
	/**
	 * Answer report
	 *
	 * @var integer
	 * @access private
	 */
	private  $report;
	
	/**
	 * Answer read
	 *
	 * @var integer
	 * @access private
	 */
	private  $read;
	
	/**
	 * Comment id
	 *
	 * @var Comment
	 * @access private
	 */
	private  $commentId;
	
	/**
	 * User id
	 *
	 * @var User
	 * @access private
	 */
	private  $userId;
	
	
	/**
	 * @access public
	 * @return integer
	 */
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	
	/**
	 * @access public
	 * @return string
	 */
	
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
		return $this;
	}
	
	
	/**
	 * @access public
	 * @return integer
	 */
	
	public function getReport() {
		return $this->report;
	}
	
	public function setReport($report) {
		$this->report = $report;
		return $this;
	}
	
	
	/**
	 * @access public
	 * @return integer
	 */
	
	public function getRead() {
		return $this->read;
	}
	
	public function setRead($read) {
		$this->read = $read;
		return $this;
	}		
	
	/**
	 * @access public
	 * @return Comment
	 */
	
	public function getCommentId() {
		return $this->commentId;
	}
	
	public function setCommentId(Comment $commentId) {
		$this->commentId = $commentId;
		return $this;
	}
	
	/**
	 * @access public
	 * @return User
	 */
	
	public function getUserId() {
		return $this->userId;
	}
	
	public function setUser(User $userId) {
		$this->userId = $userId;
		return $this;
	}
	
	
}