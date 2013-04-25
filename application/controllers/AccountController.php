<?php

/**
 * @author MFZ
 * @version 1.0
 *
 */

class AccountController extends Zend_Controller_Action
{
	
	public function init() {
		$this->translation = Zend_Registry::get('Zend_Translate');
		$this->sGlobal = new Zend_Session_Namespace("Global");
	}
	
    public function indexAction()
    {
    	if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
    		$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
    	}
    	 
    	$dbCars = new Model_Account_DbCars();

    	$cars = $dbCars->getCarsByUserId($this->sGlobal->uId);
		
    	$this->view->cars = $cars;
    	
    	$this->view->addCarLink = $this->_helper->url->url(array('controller'=>'Account', 'action'=>'addcar'), 'default', true);
		
		$this->view->deleteCarLink = $this->_helper->url->url(array('controller'=>'Account', 'action'=>'deletecar'), 'default', true);

		$this->view->editCarLink = $this->_helper->url->url(array('controller'=>'Account', 'action'=>'editcar'), 'default', true);
		
    }
    
    public function addcarAction()
    {
    	if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
    		$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
    	}
    	 
    	$addCarForm = $this->getAddCarForm();
    	$this->view->addCarForm = $addCarForm;
    	
    	$dbCars = new Model_Account_DbCars();
    	
    	$request = $this->getRequest ();
    	
    	if ($request->isPost ()) {
	    	if (! $addCarForm->isValid ( $request->getPost () )) {
	    		$this->view->addCarForm = $addCarForm;
	    	} else {
	    		$carValues = $addCarForm->getValues ();
	    		$carValues['c_User'] = $this->sGlobal->uId;
	    		if($dbCars->addCar($carValues)) {
	    			echo "ok";
					$this->_helper->redirector->gotoRoute ( array( 'controller' => 'account', 'action' => 'index'), 'default');
	    		} else {
	    			echo "nop!";
	    		}
	    	}
    	}
    }
    
	public function deletecarAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 
		
		$dbCars = new Model_Account_DbCars();
		$request = $this->getRequest ();
		$dbCars->deleteCar($request->getParam('id'));
		$this->_helper->redirector->gotoRoute ( array( 'controller' => 'account', 'action' => 'index'), 'default');
	}
	
	public function editcarAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 
		$editCarForm = $this->getEditCarForm();
		$dbCars = new Model_Account_DbCars();
		
		$request = $this->getRequest ();
		$carData = $dbCars->getCarDataById($request->getParam('id'));

		$editCarForm->populate($carData);		
    	$this->view->editCarForm = $editCarForm;
		
		if ($request->isPost ()) {
	    	if (! $editCarForm->isValid ( $request->getPost () )) {
	    		$this->view->editCarForm = $editCarForm;
	    	} else {
	    		$carValues = $editCarForm->getValues ();
	    		if($dbCars->editCar($carValues)) {
	    			echo "ok";
					$this->_helper->redirector->gotoRoute ( array( 'controller' => 'account', 'action' => 'index'), 'default');
	    		} else {
	    			echo "nop!";
	    		}
	    	}
    	}
		
	}
	
    private function getAddCarForm()
    {  
    	return new Form_Account_AddCarForm( array (
    			'action' => $this->_helper->url->url(array('controller'=>'account', 'action'=>'addcar'), 'default'),
    			'method' => 'post'
    	) );
    }
	
	private function getEditCarForm()
    {  
    	return new Form_Account_EditCarForm( array (
    			'action' => $this->_helper->url->url(array('controller'=>'account', 'action'=>'editcar'), 'default'),
    			'method' => 'post'
    	) );
    }
}
