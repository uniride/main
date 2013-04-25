<?php
/**
 * Diese Datei beinhaltet den RideController
 */
 
/**
 * 
 * @package Uniride\Controllers
 *
 */
class RideController extends Zend_Controller_Action
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
	 * ...
	 * 
	 * @return void
	 */
    public function indexAction()
    {    	
    	if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
    		$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
    	}
    	
    	$dbTravelpoints = new Model_Ride_DbTravelpoints();
    	$this->view->travelpoints = $dbTravelpoints->getTravelpointsByUserId(0);

		$dbCars = new Model_Account_DbCars();
		$this->view->cars = $dbCars->getCarsByUserId($this->sGlobal->uId);
		
		$this->view->headScript()->appendFile(
				'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places',
				'text/javascript'
		);
		$this->view->headScript()->appendFile(
				'/js/datepicker.js',
				'text/javascript'
		);
		$this->view->headScript()->appendFile(
				'/js/maps.js',
				'text/javascript'
		);
	}
	
	/**
	 * Funktion zum Speichern der Route
	 *
	 * @return void
	 */
	public function saverouteAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 	 
		if (isset($_POST['start']) && $_POST['start'] != '') {
			$dbTravelpoints = new Model_Ride_DbTravelpoints();
			$dbRoutes = new Model_Ride_DbRoutes();
			
			$startData			= str_replace('"', '', json_decode($_POST['start'], true));
			$destinationData	= str_replace('"', '', json_decode($_POST['destination'], true));
			$distance			= str_replace('"', '', $_POST['distance']);
			$duration			= str_replace('"', '', $_POST['duration']);
			$stepsData			= str_replace('"', '', json_decode($_POST['stepsData'], true));
			$dateAndTime		= str_replace('"', '', json_decode($_POST['dateAndTime'], true));
			$freeSeats			= str_replace('"', '', json_decode($_POST['seats'], true));
			$toleranceDuration	= str_replace('"', '', json_decode($_POST['toleranceDuration'], true));
			$toleranceDistance	= str_replace('"', '', json_decode($_POST['toleranceDistance'], true));
			
			$startPoint = $dbTravelpoints->getTravelpointsByKoords($startData['lat'], $startData['lng']);
			if(!$startPoint) {
				$startPoint = $dbTravelpoints->saveTravelpoint($startData, $this->sGlobal->uId);
			} else {
				$startPoint = $startPoint['t_id'];
			}
			
			$destination = $dbTravelpoints->getTravelpointsByKoords($destinationData['lat'], $destinationData['lng']);
			
			$dbRoutes->saveRoute($this->sGlobal->uId, $dateAndTime, $freeSeats, $toleranceDuration, $toleranceDistance, $distance, $duration, $destination['t_id'], $startPoint, $stepsData);
			
		}
	}
	
	/**
	 * Funktion zum Löschen einer Route
	 * 
	 * @return void
	 */
	public function deleterideAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 
		$dbRoutes = new Model_Ride_DbRoutes();
		$request = $this->getRequest ();
		$dbRoutes->deleteRide($request->getParam('id'));
		$this->_helper->redirector->gotoRoute ( array( 'controller' => 'ride', 'action' => 'show'), 'default');
		
	}
	
	/**
	 * Funktion zum Anzeigen der Routen anhand der UserId.
	 * 
	 * @link http://www.uniride.de/de/ride/show
	 * @return void
	 */
	public function showAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 
		$dbRoutes = new Model_Ride_DbRoutes();
		
		$routes = $dbRoutes->getRoutesByUserId($this->sGlobal->uId);
		
		$this->view->routes = $routes;
		
		$this->view->deleteRideLink = $this->_helper->url->url(array('controller'=>'Ride', 'action'=>'deleteride'), 'default', true);
		
	}
	
	/**
	* Fachlogik für die Fahrtanfragen
	*
	* @return void
	*/
	public function riderequestAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 
		$dbTravelpoints = new Model_Ride_DbTravelpoints();
		$this->view->travelpoints = $dbTravelpoints->getTravelpointsByUserId(0);
		
	}
	
	/**
	 * Funktion zum Speichern der Fahrtsuche
	 *
	 * @return void
	 */
	public function saverouterequestAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 
		if (isset($_POST['start']) && $_POST['start'] != '') {
			$dbTravelpoints = new Model_Ride_DbTravelpoints();
			$dbRouterequests = new Model_Ride_DbRouterequests();
				
			$startData			= str_replace('"', '', json_decode($_POST['start'], true));
			$destinationData	= str_replace('"', '', json_decode($_POST['destination'], true));
			$toleranceDuration	= str_replace('"', '', json_decode($_POST['toleranceDuration'], true));
			$dateAndTime		= str_replace('"', '', json_decode($_POST['dateAndTime'], true));
			
			$startPoint = $dbTravelpoints->getTravelpointsByKoords($startData['lat'], $startData['lng']);
			if(!$startPoint) {
				$startPoint = $dbTravelpoints->saveTravelpoint($startData, $this->sGlobal->uId);
			} else {
				$startPoint = $startPoint['t_id'];
			}
			
			$destination = explode(",", $destinationData['koords']);
			$destinationId = $dbTravelpoints->getTravelpointsByKoords($destination[0], $destination[1]);
			
			$dbRouterequests->saveRouteRequest( $this->sGlobal->uId, $dateAndTime, $destinationId['t_id'], $startPoint, '00:' . $toleranceDuration . ':00');
			
		}
	}
	
	/**
	 * Gibt die relevanten Marker für die Kartenansicht zurück
	 * 
	 * @return array Array mit Marker-Informationen
	 */
	public function getrelevantmarkersAction()
	{
		if (!isset($this->sGlobal->uId) && !Zend_Auth::getInstance ()->hasIdentity ()) {
			$this->_helper->redirector->gotoRoute ( array('action' => 'index', 'controller' => 'auth'), 'default' );
		}
		 
		$dbTravelpoints = new Model_Ride_DbTravelpoints();
		
		$travelpoints = $dbTravelpoints->getRelevantTravelpointsByDate($_POST['date']);
		
		$input = str_replace('"', '', json_decode($_POST['inputfield'], true));
		
		$array = array();
		foreach ($travelpoints as $travelpoint) {
			if($this->computeDistance($travelpoint['t_lat'], $travelpoint['t_lng'], $input['lat'], $input['lng']) < 3000) {
				$contentString = $travelpoint['t_name'] . '<br /><br />
								<a href="javascript:void(0)" onclick="addWaypoint('.$travelpoint['t_lat'].','.$travelpoint['t_lng'].');">Mitnehmen</a>
								&nbsp;&nbsp;
								<a href="javascript:void(0)" onclick="deleteWaypoint('.$travelpoint['t_lat'].','.$travelpoint['t_lng'].');">Löschen</a>';
				$array[] = array('lat' => $travelpoint['t_lat'], 'lng' => $travelpoint['t_lng'], 'contentString' => $contentString, 'title' => $travelpoint['t_name']);
			}
		}
		
		$this->_helper->layout->disableLayout();
		echo json_encode($array);
	}
	
	/**
	 * Berechnet die Distanz zwischen zwei Koordinaten
	 * 
	 * @param decimal $lat1
	 * @param decimal $lon1
	 * @param decimal $lat2
	 * @param decimal $lon2
	 * @return decimal Distanz in Metern
	 */
	private function computeDistance($lat1, $lon1, $lat2, $lon2)
	{
		$R = 6371; // km
		$dLat = deg2rad($lat2-$lat1);
		$dLon = deg2rad($lon2-$lon1);
		$lat1 = deg2rad($lat1);
		$lat2 = deg2rad($lat2);
		
		$a = sin($dLat/2) * sin($dLat/2) + sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);
		$c = 2 * atan2(sqrt($a), sqrt(1-$a));
		$d = $R * $c;
		
		return $d * 1000;
	}
}
