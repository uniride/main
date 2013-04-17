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
    	$dbAccount = new Model_Index_DbAccount();
    	
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

    	$this->view->user = $user;
    	$this->view->userProfile = $user_profile;
    	$this->view->userFriends = $user_friends;
    	
    	$this->view->variable = "index";
    }
	
    /**
     * Fachlogik für die Account-Funktion
     * 
     * @return void
     */
	public function accountAction()
    {
    	$this->view->variable = "konto";	
    }
}
