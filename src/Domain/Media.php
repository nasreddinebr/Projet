<?php
namespace BlogEcrivain\Domain;



class Media {
	
	/**
	 * Media id
	 *
	 * @var integer
	 * @access private
	 */
	private  $id;
	
	/**
	 * File name
	 *
	 * @var string
	 * @access private
	 */
	private  $fileName;
	
	/**
	 * File URL
	 *
	 * @var string
	 * @access private
	 */
	private  $urlFile;
	
	/**
	 * Post id
	 *
	 * @var Post
	 * @access private
	 */
	private  $postId;
	
	
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
	
	public function getFileName() {
		return $this->fileName;
	}
	
	public function setFileName($fileName) {
		$this->fileName = $fileName;
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	
	public function getUrlFile() {
		return $this->urlFile;
	}
	
	public function setUrlFile($urlFile) {
		$this->urlFile = $urlFile;
		return $this;
	}
	
	/**
	 * @access public
	 * @return Post
	 */
	
	public function getPostId() {
		return $this->postId;
	}
	
	public function setPostId($postId) {
		$this->postId = $postId;
		return $this;
	}
	
}