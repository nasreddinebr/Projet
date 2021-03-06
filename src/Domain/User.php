<?php
namespace BlogEcrivain\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface {
	
	/**
	 * User id
	 * @var int
	 */
	private  $id;
	
	/**
	 * User email
	 * 
	 * @var string
	 */
	private  $email;
	
	/**
	 * User name
	 * @var string
	 */
	private  $username;
	
	/**
	 * User password
	 * 
	 * @var string
	 */
	private  $password;
	
	/**
	 * User salt
	 * 
	 * @var string
	 */
	private  $salt;
	
	/**
	 * User role
	 * 
	 * @var string
	 */
	private  $role;
	
	
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
	
	public function getEmail() {
		return $this->email;
	}
	
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}
	
	
	/**
	 * @inheritDoc
	 */
	
	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}
	
	
	/**
	 * @inheritDoc
	 */
	
	public function getPassword() {
		return $this->password;
	}
	
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	
	public function getSalt() {
		return $this->salt;
	}
	
	public function setSalt($salt) {
		$this->salt = $salt;
		return $this;
	}
	
	public function getRole() {
		return $this->role;
	}
	
	public function setRole($role) {
		$this->role = $role;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getRoles() {
		return array($this->getRole());
	}
	
	/**
	 * @inheritDoc
	 */
	public function eraseCredentials(){
		
	}
}