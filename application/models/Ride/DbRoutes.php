<?php

class Model_Ride_DbRoutes extends Zend_Db_Table_Abstract
{
	protected $_name = 'routes';
	
	public function saveRoute($userId, $datetime, $freeSeats, $toleranceDuration, $toleranceDistance, $distance, $duration, $destinationId, $start, $steps) {
		$data = array(
				'r_user' => $userId,
				'r_datetime' => $datetime,
				'r_freeSeats' => $freeSeats,
				'r_toleranceDistance' => $toleranceDistance,
				'r_toleranceDuration' => $toleranceDuration,
				'r_distance' => $distance,
				'r_duration' => $duration,
				'r_destination' => $destinationId,
				'r_start' => $start
				);
		$i = 1;
		foreach($steps as $step) {
			if($i < 10) {
				$data['r_step0'.$i.'Lat'] = str_replace('"', '', $step['lat']);
				$data['r_step0'.$i.'Lng'] = str_replace('"', '', $step['lng']);
			} else {
				$data['r_step'.$i.'Lat'] = str_replace('"', '', $step['lat']);
				$data['r_step'.$i.'Lng'] = str_replace('"', '', $step['lng']);
			}
			$i++;
		}
		
		$r_Id = $this->insert($data);
		if ($r_Id > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function deleteRide($rideId) {
		$this->delete("r_id = " . $rideId);
	}
	
	public function getRoutesByUserId($userId)
	{
		$db = Zend_Registry::get('db');
		$select=$db->select();
		$select->from(
				array("r" => "routes"),
				array(
						"r.r_id",
						"r.r_datetime",
						"startpoint" => "ts.t_name",
						"endpoint" => "td.t_name",
						"r.r_distance",
						"r.r_duration"
						)
				);
		$select->join(array("td" => "travelpoints"), "r.r_destination = td.t_id");
		$select->join(array("ts" => "travelpoints"), "r.r_start = ts.t_id");
		$select->where("r.r_user = '" . $userId . "'");
		
		$qry=$select->query();
		$array=$qry->fetchAll();
		return $array;
	}
}