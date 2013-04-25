<?php

class Model_Auth_DbSession extends Zend_Db_Table_Abstract
{
	protected $_name = 'sessions';
	
	
	public function getSession($session)
	{
		$row = $this->fetchRow("sess_id = '".$session."'");
		if (!$row) {
			return false;
		}
		return $row->toArray();
	}
	
	public function updateSession($id, $session)
	{	

		$data = array(
			'user_id' => $id				
		);
		$this->update($data, "sess_id = '".  $session."'");
	}
	
	public function updateSessionEmail($Email, $session)
	{	
		$data = array(
			'user_email' => $Email				
		);
		$this->update($data, "sess_id = '".  $session."'");
	}
	
	public function deleteSession($session)
	{
		$this->delete("sess_id = '".$session."'");
	}
	
	
}