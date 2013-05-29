<?php


/**
 * @author p.stolz
 * @version 1.0
 *
 */

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	
	/**
	 * Load Config File
	 * @return void
	 * 
	 */	
	
	protected function _initConfig() {
		$config = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/config.ini' );
		Zend_Registry::set ( 'config', $config );
	}
	
	/**
	 * Initialize Zend Autoload
	 * @return void
	 */
	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader ( array ('namespace' => '', 'basePath' => APPLICATION_PATH ) );
		return $moduleLoader;
	}
	
	/**
	 * Initialize Zend Resource Loader
	 * @return void
	 */
	protected function _initResourceLoader() {
		$resourceLoader = new Zend_Loader_Autoloader_Resource ( array ('namespace' => '', 'basePath' => APPLICATION_PATH, 'resourceTypes' => array ('noxevo' => array ('path' => '/classes', 'namespace' => 'noxevo-net' ) ) ) );
		return $resourceLoader;
	}
	
	/**
	 * Initialize Zend Routing
	 * @return void
	 */
	protected function _initRouting() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$router = Zend_Controller_Front::getInstance ()->getRouter ();
				  
		/* HTTP Routen */
		
		$hostnameRoute = new Zend_Controller_Router_Route_Hostname("www.uniride.de", array(), array(), 'http');
		
		$route = new Zend_Controller_Router_Route ( ':language/:controller/:action/*', array ('language' => 'de', 'controller' => 'index', 'action' => 'index' ) );
		$router->addRoute ( 'default', $hostnameRoute->chain($route));
		
		/* HTTPS Routen */
		
		$hostnameRoute = new Zend_Controller_Router_Route_Hostname("www.uniride.de", array(), array(), 'https');
		
		$route = new Zend_Controller_Router_Route ( ':language/:controller/:action/*', array ('language' => 'de', 'controller' => 'index', 'action' => 'index' ) );
		$router->addRoute ( 'defaultSSL', $hostnameRoute->chain($route));
		
		Zend_Controller_Front::getInstance ()->setRouter ( $router );
		
	}
	
	/**
	 * Initialize Database Connection
	 * @return void
	 */
	protected function _initDatabase() {
		$config = Zend_Registry::get ( 'config' );
		$db = Zend_Db::factory ( 'Pdo_Mysql', array ('host' => $config->mysql->hostname, 'username' => $config->mysql->username, 'password' => $config->mysql->password, 'dbname' => $config->mysql->database ) );
		$db->query('SET NAMES \'utf8\'');
		Zend_Registry::set ( 'db', $db );
		
	}
	
	/**
	 * Initialize Zend Session
	 * @return void
	 */
	protected function _initSession() {
		
		$db = Zend_Registry::get ( 'db' );
		
		$config = array ('name' => 'sessions', 'primary' => 'sess_id', 'modifiedColumn' => 'sess_update', 'lifetimeColumn' => 'sess_lifetime', 'dataColumn' => 'sess_data' );
		
		Zend_Db_Table_Abstract::setDefaultAdapter ( $db );
		$session_save = new Zend_Session_SaveHandler_DbTable ( $config );
		Zend_Session::setSaveHandler ( $session_save );
		
		Zend_Session::start();
		$session = Zend_Session::getId ();
		Zend_Registry::set ( 'session', $session );

	}
	
	/**
	 * Initialize Zend Layout
	 * @return void
	 */
	protected function _initLayout() {

		$optionsLayout = array ('layout' => 'default-page', 'layoutPath' => APPLICATION_PATH . '/layouts', 'contentKey' => 'content' );
		$layout = Zend_Layout::startMvc ( $optionsLayout );		

		Zend_Registry::set ( 'layout', $layout );
		$view = $layout->getView ();
		$view->doctype ( 'XHTML1_STRICT' );
		$view->headMeta ()->appendHttpEquiv ( 'Content-Type', 'text/html;charset=UTF-8' );
		$view->headTitle ()->setSeparator ( ' - ' );
		$view->headTitle ( 'noxevo software' );
		//$view->addHelperPath('My/View/Helper/', 'My_View_Helper');		

	}
	
	/**
	 * Initialize Language Plugin
	 * @return void
	 */
	protected function _initMultiLanguage() {
		$front = Zend_Controller_Front::getInstance ();
		$front->registerPlugin ( new Plugin_Language ( ) );
	}
	
	/**
	 * Initialize Navigation
	 * @return void
	 */
	protected function _initNavigation() {
			$front = Zend_Controller_Front::getInstance ();
			$front->registerPlugin ( new Plugin_Navigation ( ) );
	}

}