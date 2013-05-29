<?php

class Model_Auth_DbLogin extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	
	public function getLogin($email)
	{
		$row = $this->fetchRow("u_Email =  '".$email."'");
		if (!$row) {
			return false;
		}
		return $row;
	}
	
	
}