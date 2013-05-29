<?php
/**
 * Diese Datei beinhaltet den IndexController
 */

/**
 * Laedt die Klasse Facebook
 */
require_once 'FacebookAPI/facebook.php';

/**
 * 
 * @package Uniride\Controllers
 *
 */
class IndexController extends Zend_Controller_Action
{
	/**
	 * Initialisiert die Klasse
	 * 
	 * Laedt die Uebersetzungen und die Session-Variablen
	 * 
	 * @return void
	 */
	public function init() {
		$this->translation = Zend_Registry::get('Zend_Translate');
		$this->sGlobal = new Zend_Session_Namespace("Global");
		$this->sAuth = new Zend_Session_Namespace("Auth");
		
		$this->view->userProfile = $this->sAuth->facebookUserProfile;
	}
	
	/**
	 * Fachlogik für die Index-Funktion
	 * 
	 * Zeigt die Userprofil-Daten an
	 * 
	 * @link http://www.uniride.de
	 * @return void
	 */
    public function indexAction()
    {	
    	if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
    		$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
    	}
    	
    	$dbAccount = new Model_Index_DbAccount();
		$dbTravelpoints = new Model_Ride_DbTravelpoints();
    	
    	/*
    	$facebook = new Facebook(array(
    			'appId'  => '329633387331',
    			'secret' => 'e7bb638a72841d0a28a117ee6c7c7eeb',
    	));
    	
    	// Get User ID
    	$user = $facebook->getUser();
    	$user_profile = null;
    	$user_friends = null;
    	if ($user) {
    		try {
    			// Proceed knowing you have a logged in user who's authenticated.
    			$user_profile = $facebook->api('/me');
    			$user_friends = $facebook->api('/me/friends');
    		} catch (FacebookApiException $e) {
    			error_log($e);
    			$user = null;
    		}
    		$this->view->logoutUrl = $facebook->getLogoutUrl();
    		
    		$UserExists = $dbAccount->checkIfUserExists($user_profile['id']);
    		if(!$UserExists) {
    			$AccountExists = $dbAccount->checkIfAccountExists($user_profile['email']);
    			if($AccountExists) {
    				$dbAccount->updateAccountWithFbId($AccountExists, $user_profile['id']);
    			} else {
    				$dbAccount->createAccount($user_profile['first_name'], $user_profile['last_name'], $user_profile['gender'], $user_profile['email'], $user_profile['id']);
    			}
    		}
    		
    		$this->sGlobal->uId = $UserExists;
    		
    	} else {
    		$this->view->loginUrl = $facebook->getLoginUrl();
    	}
    	*/

    	$this->view->user = $this->sAuth->facebookUser;
    	$this->view->userProfile = $this->sAuth->facebookUserProfile;
    	//$this->view->userFriends = $user_friends;
    	
		$destinationsArray = array();
		foreach($dbTravelpoints->getTravelpointsByUserId(0) as $destination){
			$destinationsArray[$destination['t_id']] = $destination['t_name'];
		}
		
		$this->view->requestForm = $this->getRequestForm($destinationsArray);
		$this->view->rideForm = $this->getRideForm($destinationsArray);
		
    	$this->view->logoutUrl = $this->_helper->url->url(array('controller'=>'auth', 'action'=>'logout'), 'default', true);
    	
    	$this->view->variable = "index";
    }
	
    /**
     * Fachlogik für die Account-Funktion
     * 
     * @return void
     */
	public function accountAction()
    {
    	if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
    		$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
    	}
    	$this->view->variable = "konto";	
    }
	
	public function getRequestForm($destinationsArray) 
	{
		return new Form_Index_RequestForm ( array (
		'action' => $this->_helper->url->url(array('controller'=>'ride', 'action'=>'riderequest'), 'default'),
		'method' => 'post'
		),$destinationsArray );
	}
	
	public function getRideForm($destinationsArray) 
	{
		return new Form_Index_RideForm ( array (
		'action' => $this->_helper->url->url(array('controller'=>'ride', 'action'=>'index'), 'default'),
		'method' => 'post'
		),$destinationsArray );
	}
	
}
