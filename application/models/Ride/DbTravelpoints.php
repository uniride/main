<?php
/**
* Diese Datei beinhaltet das Model Ride_DbTravelpoints
*/

/**
* Beinhaltet Funktionen zur Abfrage aus der Tabelle travelpoints
*
* @package Uniride\Models\Ride
*/
class Model_Ride_DbTravelpoints extends Zend_Db_Table_Abstract
{
	/**
	* @var string Definiert die verwendete Tabelle
	*/
	protected $_name = 'travelpoints';
	
	/**
	* Holt vorhandene Wegpunkte anhand der UserId
	*
	* @param int $userId
	* @return array Array mit Wegpunkten des Benutzers
	*/
	public function getTravelpointsByUserId($userId) {
		$row = $this->fetchAll("t_user = '" . $userId . "'");
		return $row->toArray();
	}
	
	/**
	* Holt vorhandene Wegpunkte anhand der Koordinaten
	*
	* @param decimal $lat
	* @param decimal $lng
	* @return array Array mit Daten des Wegpunktes
	*/
	public function getTravelpointsByKoords($lat, $lng) {
		$row = $this->fetchRow("t_lat = ".$lat." AND t_lng = ".$lng."");
		if($row) {
			$return = $row->toArray();
			if(is_array($return) && count($return) > 0) {
				return $return;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Holt alle Travelpoints
	 * 
	 * @return array Array mit allen Travelpoints
	 */
	public function getAllTravelpoints() {
		$row = $this->fetchAll();
		return $row->toArray();
	}
	
	/**
	 * Holt alle Travelpoints, die zeitlich im Rahmen liegen
	 * 
	 * @return array Array mit zeitlich relevanten Travelpoints
	 */
	public function getRelevantTravelpointsByDate($date) {

		$db = Zend_Registry::get('db');
		$select=$db->select();
		$select->from(
				array("rq" => "routerequests"),
				array(
						"tp.*"
				)
		);
		$select->join(array("tp" => "travelpoints"), "rq.req_start = tp.t_id");
		$select->where("
						SUBTIME(rq.req_datetime, rq.req_toleranceDuration) <= '".$date."'
						AND
						ADDTIME(rq.req_datetime, rq.req_toleranceDuration) >= '".$date."'
						");
		
		$qry=$select->query();
		$array=$qry->fetchAll();
		return $array;
	}
	
	/**
	* Speichert einen Routenpunkt
	*
	* @return int|false ID des Wegpunktes oder false 
	*/
	public function saveTravelpoint($startData, $userId) {
		$data = array(
						't_lat' => $startData['lat'],
						't_lng' => $startData['lng'],
						't_name' => $startData['address'],
						't_user' => $userId
					);
		$t_Id = $this->insert($data);
		if($t_Id > 0) {
			return $t_Id;
		} else {
			return false;
		}
	}
}