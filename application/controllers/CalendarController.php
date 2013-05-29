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
		$this->sAuth = new Zend_Session_Namespace("Auth");
		
		$this->view->userProfile = $this->sAuth->facebookUserProfile;
	}
	
    public function indexAction()
    {
    	if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
    		$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
    	}
    	$this->view->headScript()->appendFile(
    			'/js/fullcalendar.min.js',
    			'text/javascript'
    	);
    	$this->view->headScript()->appendFile(
    			'/js/uniride_calendar.js',
    			'text/javascript'
    	);
    }
    
    public function getcalendarentriesAction() {
    	$dbRoutes = new Model_Calendar_DbRoutes;
    	
    	$routes = $dbRoutes->getRoutesByUserId($this->sGlobal->uId);
    	
    	$return = array();
    	$i = 1;
    	foreach ($routes as $route) {
    		$return[] = array(
		    	'id' => $i,
		    	'title' => $route['startpoint'],
		    	'start' => substr($route['r_datetime'],0,10)
		    	);
    		$i++;
    	}
    	$this->_helper->layout->disableLayout();
    	echo json_encode($return);
    }
}
