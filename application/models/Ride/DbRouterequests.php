<?php
/**
* Diese Datei beinhaltet das Model Ride_DbRouterequests
*/

/**
* Beinhaltet Funktionen zur Abfrage aus der Tabelle requests
*
* @package Uniride\Models\Ride
*/
class Model_Ride_DbRouterequests extends Zend_Db_Table_Abstract
{
	/**
	* @var string Definiert die verwendete Tabelle
	*/
	protected $_name = 'routerequests';
	
	/**
	* Speichert Mitfahrtgesuche
	*
	* @param int $userId
	* @param datetime $datetime
	* @param int $destinationId
	* @param int $startId
	* @param int $toleranceDuration
	* @return boolean
	*/
	public function saveRouteRequest($userId, $datetime, $destinationId, $startId, $toleranceDuration) {
		$data = array(
				'req_user' => $userId,
				'req_datetime' => $datetime,
				'req_destination' => $destinationId,
				'req_start' => $startId,
				'req_toleranceDuration' => $toleranceDuration
			);
		
		$req_Id = $this->insert($data);
		if ($req_Id > 0) {
			return true;
		} else {
			return false;
		}
	}
}