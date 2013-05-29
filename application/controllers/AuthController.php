<?php

/**
 * @author p.stolz
 * @version 1.0
 *
 */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

/**
 * Laedt die Klasse Facebook
 */
require_once 'FacebookAPI/facebook.php';

class AuthController extends Zend_Controller_Action
{
	public function init() {
		$this->sAuth = new Zend_Session_Namespace("Auth");
		$this->sGlobal = new Zend_Session_Namespace("Global");
	}
	
	
    public function indexAction()
    {
        //$this->view->loginForm = $this->getLoginForm ();
        
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
    			//$user_friends = $facebook->api('/me/friends');
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
    		
    		$this->sAuth->facebookUserProfile = $user_profile;
    		$this->sAuth->facebookUser = $user;
    	
    		$this->sGlobal->uId = $UserExists;
    		
    		$this->_helper->redirector->gotoRoute ( array( 'controller' => 'index', 'action' => 'index'), 'default' );
    	
    	} else {
    		$this->view->loginUrl = $facebook->getLoginUrl();
			$this->view->registrationUrl = $this->_helper->url->url(array('controller'=>'auth', 'action'=>'registration'), 'default');
			$this->view->loginForm = $this->getLoginForm();
    	}
    }
    
	public function getAuthAdapter(array $params) {		
		$db = Zend_Registry::get('db');
		
		// Create a database Auth Adapter         
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
	       $authAdapter
	           ->setTableName('users')
	           ->setIdentityColumn('u_Email')
	           ->setCredentialColumn('u_Password')
	           ->setCredentialTreatment('MD5(?)')    
	       ;           
	       $authAdapter
	           ->setIdentity($params['email'])
	           ->setCredential($params['password'])	                 	            
	       ;
	    return $authAdapter;
	}
    
    
	public function processAction()
    {
    	$loginForm = $this->getLoginForm ();
    	$this->view->loginForm = $loginForm;
    	$request = $this->getRequest ();
    	$loginDb = new Model_Auth_DbLogin();
    	//$request = Zend_Controller_Front::getInstance()->getRequest();
    	
		if (! $request->isPost ()) {				
				return $this->_helper->redirector ( 'index' );
			}
		
			if (! $loginForm->isValid ( $request->getPost () )) {							
				$this->view->loginForm = $loginForm;
				return $this->render ( 'index' );	
			} else {
				$loginValues = $loginForm->getValues ();

				$adapter = $this->getAuthAdapter( $loginValues );
				$auth = Zend_Auth::getInstance ();
				$result = $auth->authenticate ( $adapter );
				
				if (!($result->isValid ()))
				{	
						$loginForm->setDescription ( "Benutzername oder Passwort falsch!" );
						$this->view->loginForm = $loginForm;
						return $this->render ( 'index' );
				}
				else 
				{			
					$sessionDb = new Model_Auth_DbSession();	
					$user = $loginDb->getLogin($loginValues["email"]);						
					$session = Zend_Session::getId();
					$this->sAuth->Id = $user['u_Id'];
					$this->sGlobal->uId = $user['u_Id'];
					$this->sAuth->Firstname = $user["u_Firstname"];
					$this->sAuth->Lastname = $user["u_Lastname"];
					$this->sAuth->Email = $user["u_Email"];
					if($user["u_IsAdmin"]) {
						$this->sAuth->isAdmin = true;
					} else {
						$this->sAuth->isAdmin = false;
					}
					if($this->sAuth->isAdmin) {
						$this->_helper->redirector->gotoRoute ( array( 'controller' => 'index', 'action' => 'index'), 'default' );
					} else {
						$this->_helper->redirector->gotoRoute ( array( 'controller' => 'index', 'action' => 'index'), 'default' );
					}							
				}	
			}
    }
	
	public function registrationAction(){
		$registrationForm = $this->getRegistrationForm();
    	$this->view->registrationForm = $registrationForm;
    	
    	$dbUsers = new Model_Index_DbAccount();
    	
    	$request = $this->getRequest ();
    	
    	if ($request->isPost ()) {
	    	if (! $registrationForm->isValid ( $request->getPost () )) {
	    		$this->view->registrationForm = $registrationForm;
	    	} else {
	    		$regValues = $registrationForm->getValues ();
	    		if($dbUsers->checkIfAccountExists($regValues['u_Email']) == false) {
		    		if($dbUsers->createAccount($regValues['u_Firstname'], $regValues['u_Lastname'], $regValues['u_Gender'], $regValues['u_Email'], 0, md5($regValues['u_Password']))) {
						$this->_helper->redirector->gotoRoute ( array( 'controller' => 'index', 'action' => 'index'), 'default');
		    		} else {
		    			echo "Account konnte nicht erstellt werden!";
		    		}
	    		} else {
	    			echo "Ein Account mit dieser Email existiert bereits!";
	    		}
	    	}
    	}
	}
    
	public function getLoginForm() 
	{
		return new Form_Auth_LoginForm ( array (
		'action' => $this->_helper->url->url(array('controller'=>'auth', 'action'=>'process'), 'default'),
		'method' => 'post'
		) );
	}
	
	public function getRegistrationForm() 
	{
		return new Form_Auth_RegistrationForm ( array (
		'action' => $this->_helper->url->url(array('controller'=>'auth', 'action'=>'registration'), 'default'),
		'method' => 'post'
		) );
	}
    
	public function logoutAction() 
	{
		$session =  Zend_Session::getId();
		$sessionDb = new Model_Auth_DbSession(); 		
		$sessionDb->deleteSession($session);
		Zend_Auth::getInstance()->clearIdentity ();
		Zend_Session::destroy(true);
		$this->_helper->redirector->gotoRoute ( array('action' => 'index'), 'default' );
	}
}
