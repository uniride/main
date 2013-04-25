<?php

class Model_Auth_DbLogin extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	
	public function getLogin($user)
	{
		$row = $this->fetchRow("user =  '".$user."'");
		if (!$row) {
			return false;
		}
		return $row;
	}
	
	
}