<?php

class Model_Calendar_DbRoutes extends Zend_Db_Table_Abstract
{
	protected $_name = 'routes';
	
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