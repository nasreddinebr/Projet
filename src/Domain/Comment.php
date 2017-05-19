<?php
namespace BlogEcrivain\Domain;


class Comment {
	/**
	 * Comment id
	 * 
	 * @var integer
	 */
	private $id;
	
	/**
	 * Comment date
	 * 
	 * @var \DateTime 
	 */
	private $dateComment;
	
	/**
	 * Comment content
	 * 
	 * @var string
	 */
	private $content;
	
	/**
	 * Comment report
	 *
	 * @var integer
	 */
	private $report;
	
	/**
	 * Comment read
	 *
	 * @var integer
	 */
	private $read;
	
	/**
	 * Associated post
	 *
	 * @var \BlogEcrivain\Domain\Post
	 */
	private $post;
	
	/**
	 * Comment user
	 *
	 * @var integer
	 */
	private $user;
	
	/**
	 * Comment author.
	 *
	 * @var \BlogEcrivain\Domain\User
	 */
	private $author;
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
		return $this->id;
	}
	
	public function getDateComment() {
		return $this->dateComment;
	}
	
	public function setDateComment($dateComment) {
		$this->dateComment = $dateComment;
		return $this->dateComment;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
		return $this->content;
	}
	
	public function getReport() {
		return $this->report;
	}
	
	public function setReport($report) {
		$this->report = $report;
		return $this->report;
	}
	
	public function getRead() {
		return $this->read;
	}
	
	public function setRead($read) {
		$this->read = $read;
		return $this->read;
	}
	
	public function getPost() {
		return $this->post;
	}
	
	public function setPost(Post $post) {
		$this->post = $post;
		return $this->post;
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function setUser($user) {
		$this->user = $user;
		return $this->user;
	}
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function setAuthor(User $author) {
		$this->author = $author;
		return $this;
	}
}