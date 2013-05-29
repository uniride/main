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
	        					'label' 		=> '',
	        					'action' 		=> 'riderequest',
	        					'controller'	=> 'ride',
	        					'type'			=> 'Zend_Navigation_Page_Mvc',
	        					'route' 		=> 'default',
	        					'class' 		=> 'search'
	        			),
	        			array(
	        					'label' 		=> '',
	        					'action' 		=> 'index',
	        					'controller'	=> 'ride',
	        					'type'			=> 'Zend_Navigation_Page_Mvc',
	        					'route' 		=> 'default',
	        					'class' 		=> 'offer'
	        			),
	        			array(
	        					'label' 		=> '',
	        					'action' 		=> 'index',
	        					'controller'	=> 'calendar',
	        					'type'			=> 'Zend_Navigation_Page_Mvc',
	        					'route' 		=> 'default',
	        					'class' 		=> 'calendar'
	        			),
	        			array(
	        				'label' 		=> '',
	        				'action' 		=> 'index',
	        				'controller'	=> 'account',
	       					'type'			=> 'Zend_Navigation_Page_Mvc',
	       					'route' 		=> 'default',
	       					'class' 		=> 'account'
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
