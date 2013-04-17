<?php
/**
 * Diese Datei beinhaltet das Model Account_DbCars
 */

/**
 * Beinhaltet Funktionen zur Abfrage aus der Tabelle cars
 *
 * @package Uniride\Models\Account
 */
class Model_Account_DbCars extends Zend_Db_Table_Abstract
{
	/**
	 * @var string Definiert die verwendete Tabelle
	 */
	protected $_name = 'cars';
	
	/**
	 * Gibt ein Array mit allen Autos eines Users zurück
	 * 
	 * @param int $userId interne UserId
	 * @return array Array von Autos
	 */
	public function getCarsByUserId($userId)
	{
		$row = $this->fetchAll("c_User = '" . $userId . "'");
		return $row->toArray();	
	}
	
	/**
	 * Fuegt ein Auto in die Datenbank ein
	 * 
	 * @param array $carData
	 * @return boolean
	 */
	public function addCar($carData) {
		$c_Id = $this->insert($carData);
		if ($c_Id > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function deleteCar($carID) {
		$this->delete("c_Id = " . $carID);
	}
	
	public function getCarDataById($carID) {
		$row = $this->fetchRow("c_Id = " . $carID);
		return $row->toArray();
	}
	
	public function editCar($carData) {
		$c_Id = $this->update($carData, "c_Id = " . $carData['c_Id']);
		if ($c_Id > 0) {
			return true;
		} else {
			return false;
		}
	}
	
}