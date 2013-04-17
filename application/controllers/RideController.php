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
	public function saverouteAction() {
		 
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
	public function deleterideAction() {
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
	public function showAction() {
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
	public function riderequestAction() {
		$dbTravelpoints = new Model_Ride_DbTravelpoints();
		$this->view->travelpoints = $dbTravelpoints->getTravelpointsByUserId(0);
		
	}
	
	/**
	 * Funktion zum Speichern der Fahrtsuche
	 *
	 * @return void
	 */
	public function saverouterequestAction() {
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
			
			$dbRouterequests->saveRouteRequest( $this->sGlobal->uId, $dateAndTime, $destinationId['t_id'], $startPoint, $toleranceDuration);
			
		}
	}
	
	/**
	 * Gibt die relevanten Marker für die Kartenansicht zurück
	 * 
	 * @return array Array mit Marker-Informationen
	 */
	public function getrelevantmarkersAction() {
		
		$dbTravelpoints = new Model_Ride_DbTravelpoints();
		
		$travelpoints = $dbTravelpoints->getAllTravelpoints();
		
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
	
	private function computeDistance2( $lat1, $lon1, $lat2, $lon2)
	{
		// Based on http://www.ngs.noaa.gov/PUBS_LIB/inverse.pdf
		// using the "Inverse Formula" (section 4)
	
		$MAXITERS = 20;
		// Convert lat/long to radians
		$lat1 *= pi() / 180.0;
		$lat2 *= pi() / 180.0;
		$lon1 *= pi() / 180.0;
		$lon2 *= pi() / 180.0;
	
		$a = 6378137.0; // WGS84 major axis
		$b = 6356752.3142; // WGS84 semi-major axis
		$f = ($a - $b) / $a;
		$aSqMinusBSqOverBSq = ($a * $a - $b * $b) / ($b * $b);
	
		$L = $lon2 - $lon1;
		$A = 0.0;
		$U1 = atan((1.0 - $f) * tan($lat1));
		$U2 = atan((1.0 - $f) * tan($lat2));
	
		$cosU1 = cos($U1);
		$cosU2 = cos($U2);
		$sinU1 = sin($U1);
		$sinU2 = sin($U2);
		$cosU1cosU2 = $cosU1 * $cosU2;
		$sinU1sinU2 = $sinU1 * $sinU2;
	
		$sigma = 0.0;
		$deltaSigma = 0.0;
		$cosSqAlpha = 0.0;
		$cos2SM = 0.0;
		$cosSigma = 0.0;
		$sinSigma = 0.0;
		$cosLambda = 0.0;
		$sinLambda = 0.0;
	
		$lambda = $L; // initial guess
		for ($iter = 0; $iter < $MAXITERS; $iter++)
		{
			$lambdaOrig = $lambda;
			$vcosLambda = cos($lambda);
			$sinLambda = sin($lambda);
			$t1 = $cosU2 * $sinLambda;
			$t2 = $cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda;
			$sinSqSigma = $t1 * $t1 + $t2 * $t2; // (14)
			$sinSigma = sqrt($sinSqSigma);
			$cosSigma = $sinU1sinU2 + $cosU1cosU2 * $cosLambda; // (15)
			$sigma = atan2($sinSigma, $cosSigma); // (16)
			$sinAlpha = ($sinSigma == 0) ? 0.0 : $cosU1cosU2 * $sinLambda / $sinSigma; // (17)
			$cosSqAlpha = 1.0 - $sinAlpha * $sinAlpha;
			$cos2SM = ($cosSqAlpha == 0) ? 0.0 : $cosSigma - 2.0 * $sinU1sinU2 / $cosSqAlpha; // (18)
	
			$uSquared = $cosSqAlpha * $aSqMinusBSqOverBSq; // defn
			$A = 1 + ($uSquared / 16384.0) * (4096.0 + $uSquared * (-768 + $uSquared * (320.0 - 175.0 * $uSquared)));
			$B = ($uSquared / 1024.0) * (256.0 + $uSquared * (-128.0 + $uSquared * (74.0 - 47.0 * $uSquared)));
			$C = ($f / 16.0) * $cosSqAlpha * (4.0 + $f * (4.0 - 3.0 * $cosSqAlpha)); // (10)
			$cos2SMSq = $cos2SM * $cos2SM;
			$deltaSigma = $B * $sinSigma * ($cos2SM + ($B / 4.0) * ($cosSigma * (-1.0 + 2.0 * $cos2SMSq) - ($B / 6.0) * $cos2SM * (-3.0 + 4.0 * $sinSigma * $sinSigma) * (-3.0 + 4.0 * $cos2SMSq)));
	
			$lambda = $L + (1.0 - $C) * $f * $sinAlpha * ($sigma + $C * $sinSigma * ($cos2SM + $C * $cosSigma * (-1.0 + 2.0 * $cos2SM * $cos2SM))); // (11)
	
			$delta = ($lambda - $lambdaOrig) / $lambda;
	
			if (abs($delta) < exp(-12))
				break;
		}
	
		return ($b * $A * ($sigma - $deltaSigma)) / 1000;
	}
}
