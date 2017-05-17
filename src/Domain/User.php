<?php
namespace BlogEcrivain\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface {
	
	/**
	 *
	 * @var int
	 * @access private
	 */
	private  $id;
	
	/**
	 *
	 * @var string
	 * @access private
	 */
	private  $email;
	
	/**
	 *
	 * @var string
	 * @access private
	 */
	private  $login;
	
	/**
	 *
	 * @var string
	 * @access private
	 */
	private  $password;
	
	/**
	 *
	 * @var string
	 * @access private
	 */
	private  $salt;
	
	/**
	 *
	 * @var string
	 * @access private
	 */
	private  $role;
	
	
	/**
	 * @access public
	 * @return integer
	 */
	
	public function getId() {
		return $this->id;
	}
	
	
	/**
	 * @access public
	 * @param integer $id
	 *
	 */
	
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
	
	public function getLogin() {
		return $this->login;
	}

	public function setLogin($login) {
		$this->login = $login;
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
	
	
	/**
	 * @access public
	 * @return string
	 */
	
	public function getRole() {
		return $this->role;
	}
	
	public function setRole($role) {
		$this->role = $role;
		return $this;
	}
	
	public function eraseCredentials(){
		
	}
}