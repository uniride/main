<?php
/**
 * Diese Datei beinhaltet das Model Index_DbAccount
 */

/**
 * Beinhaltet Funktionen zur Abfrage aus der Tabelle users
 *
 * @package Uniride\Models\Index
 */
class Model_Index_DbAccount extends Zend_Db_Table_Abstract
{
	/**
	 * @var string Definiert die verwendete Tabelle
	 */
	protected $_name = 'users';
	
	/**
	 * Ueberprueft ob zur facebookId breits ein interner User existiert
	 * 
	 * @param int $facebookId
	 * @return int|false interne UserId or false
	 */
	public function checkIfUserExists($facebookId) {
		$row = $this->fetchAll("u_FBId = '" . $facebookId . "'");
		$result = $row->toArray();
		if($result) {
			return $result[0]['u_Id'];
		} else {
			return false;
		}
	}
	
	/**
	 * Ueberprueft ob zur Emailadresse bereits ein interner User existiert
	 * 
	 * @param string $email
	 * @return int|false interne UserId or false
	 */
	public function checkIfAccountExists($email) {
		$row = $this->fetchAll("u_Email = '" . $email . "'");
		$result = $row->toArray();
		if($result) {
			return $result[0]['u_Id'];
		} else {
			return false;
		}
	}
	
	/**
	 * Ergaenzt einen internen Account mit der facebookId
	 * 
	 * @param int $u_Id
	 * @param int $facebookId
	 * @return void
	 */
	public function updateAccountWithFbId($u_Id, $facebookId) {
		$data = array('u_FBId' => $facebookId);
		$this->update($data, "u_Id = '".  $u_Id ."'");
	}
	
	/**
	 * Erstellt einen neuen internen Account
	 * 
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $gender
	 * @param string $email
	 * @param int $id FacebookId
	 * @return bool
	 */
	public function createAccount($firstName, $lastName, $gender, $email, $id) {
		$data = array('u_FBId' => $id, 'u_Lastname' => $lastName, 'u_Firstname' => $firstName, 'u_Gender' => $gender, 'u_Email' => $email);

		$u_Id = $this->insert($data);
		if ($u_Id > 0) {
			return true;
		} else {
			return false;
		}
	}
}