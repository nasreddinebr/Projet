<?php
namespace BlogEcrivain\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use BlogEcrivain\Domain\User;


class UserDAO extends DAO implements UserProviderInterface {
	/**
	 * Recuperate a user by id
	 * 
	 * @param integer $id  User id
	 * @return user
	 */
	public function recoverUserById($id) {
		$req = "SELECT * FROM users WHERE id_user=?";
		$row = $this->getDb()->fetchAssoc($req, array($id));
		if ($row)
			return $this->buildDomainObject($row);
		else
			throw new \Exception("Aucun identifiant correspondant à l'id utilisateur" . $id);
	
	}
	
	/**
	 * @inheritDoc
	 */
	public function loadUserByUsername($username) {
		$req = "SELECT * FROM users WHERE login=?";
		$row = $this->getDb()->fetchAssoc($req, array($username));
		if ($row){
			return $this->buildDomainObject($row);
		}else {
			throw new UsernameNotFoundException(sprintf('Uilisateur "%s" non trouver.', $username));
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function refreshUser(UserInterface $user) {
		$class = get_class($user);
		if (!$this->supportsClass($class)) {
			throw new UnsupportedUserException(sprintf('Les instances de "%s" ne sont pas prises en charge.', $class));
		}
		return $this->loadUserByUsername($user->getUsername());
	}
	
	/**
	 * @inheritDoc
	 */
	public function supportsClass($class){
		return 'BlogEcrivain\Domain\User' === $class;
	}
	
	/**
	 * Recover a list of all users
	 *
	 * @return array
	 */
	public function recoverAllUsers() {
		$req = "SELECT * FROM users ORDER BY id_user";
		$response = $this->getDb()->fetchAll($req);
		
		//Convert Query response to an array of domain objects
		$users = array();
		foreach ($response as $row) {
			$userId = $row['id_user'];
			$users[$userId] = $this->buildDomainObject($row);
		}
		return $users;
	}
	
	/**
	 * Add a user into the DB or update it
	 * 
	 * @param \BlogEcrivain\Domain\User $user to save.
	 */
	public function addUser(User $user) {
		$userData = array(
			'email' 	=> $user->getEmail(),
			'login' 	=> $user->getUsername(),
			'password' 	=> $user->getPassword(),
			'salt'		=> $user->getSalt(),
			'role'		=> $user->getRole()
		);
		if ($user->getId()) {
			// If the user already exists: upadate it
			$this->getDb()->update('users', $userData, array('id_user' => $user->getId()));
		}else {
			// Add new user
			$this->getDb()->insert('users', $userData);
			
			// Get id of newly created user and set it 
			$id = $this->getDb()->lastInsertId();
			$user->setId($id);
		}
	}
	
	/**
	 * Remove a user
	 * 
	 * @param integer $id
	 */
	public function removeUser($id) {
		$this->getDb()->delete('users', array('id_user' => $id));
	}
	
	/**
	 * Creation of the user object based on the row of the database.
	 *
	 * @param array $row The DB row containing User data.
	 * @return BlogEcrivain\Domain\User
	 */
	protected function buildDomainObject(array $row) {
		$user = new User();
		$user->setId($row['id_user']);
		$user->setEmail($row['email']);
		$user->setUsername($row['login']);
		$user->setPassword($row['password']);
		$user->setSalt($row['salt']);
		$user->setRole($row['role']);
		return $user;
	}
}