<?php
namespace BlogEcrivain\Domain;

class Post {
	/**
	 * Post id
	 * 
	 * @var integer
	 */
	private $id;
	
	/**
	 * Post title
	 * 
	 * @var string
	 */
	private $title;
	
	/**
	 * Post p_date
	 * 
	 * @var \DateTime
	 */
	private $p_date;
	
	/**
	 * Post content
	 * 
	 * @var string
	 */
	private $content;
	
	/**
	 * Post author.
	 *
	 * @var \BlogEcrivain\Domain\User
	 */
	private $author;
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
		return $this;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setTitle($title){
		$this->title = $title;
		return $this;
	}
	
	public function getDate(){
		return $this->p_date;
	}
	
	public function setDate($p_date){
		$this->p_date = $p_date;
		return $this;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function setContent($content){
		$this->content = $content;
		return $this;
	}
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function setAuthor(User $author) {
		$this->author = $author;
		return $this;
	}
}