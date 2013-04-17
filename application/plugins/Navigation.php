<?php

class Plugin_Navigation extends Zend_Controller_Plugin_Abstract {
	
	public function createNavigation() {

		$navigation = new Zend_Navigation(
		array(
			array(
		        	'label'      	=> 'MainNavigation',
		        	'type'         	=> 'Zend_Navigation_Page_Mvc',
		        	'pages'     	=> 
		        	array(
			            array(
			                'label' 		=> 'Dashboard',
			                'action' 		=> 'index',
			            	'type'			=> 'Zend_Navigation_Page_Mvc',
			            	'route' 		=> 'default',
			            	'class' 		=> 'Dashboard'
			            ),
	        			array(
	        				'label' 		=> 'Konto',
	        				'action' 		=> 'index',
	        				'controller'	=> 'account',
	       					'type'			=> 'Zend_Navigation_Page_Mvc',
	       					'route' 		=> 'default',
	       					'class' 		=> 'Account'
	        			),
	        			array(
	        				'label' 		=> 'Kalender',
	        				'action' 		=> 'index',
	        				'controller'	=> 'calendar',
	       					'type'			=> 'Zend_Navigation_Page_Mvc',
	       					'route' 		=> 'default',
	       					'class' 		=> 'Calendar'
	        			)
						,
	        			array(
	        				'label' 		=> 'Fahrten',
	        				'action' 		=> 'index',
	        				'controller'	=> 'ride',
	       					'type'			=> 'Zend_Navigation_Page_Mvc',
	       					'route' 		=> 'default',
	       					'class' 		=> 'Ride'
	        			),
	        			array(
	        					'label' 		=> 'Mitfahrtgesuch',
	        					'action' 		=> 'riderequest',
	        					'controller'	=> 'ride',
	        					'type'			=> 'Zend_Navigation_Page_Mvc',
	        					'route' 		=> 'default',
	        					'class' 		=> 'Ride'
	        			),
	        			array(
	        					'label' 		=> 'Fahrten anzeigen',
	        					'action' 		=> 'show',
	        					'controller'	=> 'ride',
	        					'type'			=> 'Zend_Navigation_Page_Mvc',
	        					'route' 		=> 'default',
	        					'class' 		=> 'Ride'
	        			)
			       )
		        )
	        )
	    );
		
       return $navigation;
	}
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$layout = Zend_Registry::get('layout');
        $view = $layout->getView();  
        $view->getHelper('navigation')->navigation($this->createNavigation());
	}
	
}
