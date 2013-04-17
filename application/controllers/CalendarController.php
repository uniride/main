<?php

/**
 * @author 
 * @version 1.0
 *
 */

class CalendarController extends Zend_Controller_Action
{
	public function init() 
	{
		$this->translation = Zend_Registry::get('Zend_Translate');
		$this->sGlobal = new Zend_Session_Namespace("Global");
	}
	
    public function indexAction()
    {
		
    }
}
