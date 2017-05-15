<?php
namespace  BlogEcrivain\DAO;

use Doctrine\DBAL\Connection;

abstract class DAO {
	
	/**
	 * Database connection
	 *
	 * @var \Doctrine\DBAL\Connection
	 */
	private $db;
	
	/**
	 * Constructor
	 *
	 * @param \Doctrine\DBAL\Connection The database connection object
	 */
	public function __construct(Connection $db){
		$this->db = $db;
	}
	
	/**
	 * Grants access to the database connection object
	 * 
	 * @return \Doctrine\DBAL\Connection The database connection
	 */
	
	protected function getDb() {
		return $this->db;
	}
	
	/**
	 * Build a domain object from a DB row.
	 * 
	 * Must be overridden by child classes
	 */
	protected abstract function buildDomainObject(array $row);
}