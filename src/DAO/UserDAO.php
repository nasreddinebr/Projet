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