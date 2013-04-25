 var directionDisplay;
      var directionsService = new google.maps.DirectionsService();
      var map;
      var tmpRoute;
      var markersArray = [];
      var waypts = [];
      var inputFieldStart = [];

      function initialize() {
    	  var rendererOptions = {
  		        draggable: false
  		      };
  		      directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

        var uniSiegen = new google.maps.LatLng(50.91238440000001, 8.02557640);
        var mapOptions = {
          zoom:15,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          center: uniSiegen
        }
        map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
        directionsDisplay.setMap(map);

        var input = document.getElementById('start');
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.bindTo('bounds', map);

        var marker = new google.maps.Marker({
            map: map
        });

        google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
            computeTotalDistance(directionsDisplay.directions);
            tmpRoute = directionsDisplay.directions;

           var count = jQuery(tmpRoute.routes[0].legs[0].via_waypoint).size();
            if(count > 3) {
            	delete tmpRoute.routes[0].legs[0].via_waypoint[4];
            }
        });

        /*
        google.maps.event.addListener(map, 'dragend', function() {
           directionsDisplay.directions;
        });
		*/
        
        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
          map: map
        });

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
          infowindow.close();
          marker.setVisible(false);
          input.className = '';
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // Inform the user that the place was not found and return.
            input.className = 'notfound';
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          var image = new google.maps.MarkerImage(
              place.icon,
              new google.maps.Size(71, 71),
              new google.maps.Point(0, 0),
              new google.maps.Point(17, 34),
              new google.maps.Size(35, 35));
          marker.setIcon(image);
          marker.setPosition(place.geometry.location);

          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
          infowindow.open(map, marker);
          
          deleteOverlays();
          waypts = [];
          
          inputFieldStart = {
                  "lat": '"'+place.geometry.location.jb+'"',
                  "lng": '"'+place.geometry.location.kb+'"'
          };
	      });
      }
        
      function calcRoute() {
    	
    	  deleteOverlays();
    	  
	      var startDateReq = document.getElementById('date').value + " " + document.getElementById('hours').value + ":" + document.getElementById('minutes').value + ":00";
	      
	      $.post("ride/getrelevantmarkers", { inputfield: JSON.stringify(inputFieldStart), date: startDateReq }, function(data) {
	      	$.each(data, function(index, value) {
	      		createMarker(map, value.lat, value.lng, value.contentString, value.title);
	      	});
	      }, "json");

    
        var start = document.getElementById('start').value;
        var endLatLng = document.getElementById('end').value;
        var endKor = endLatLng.split(",");
        var end = new google.maps.LatLng(endKor[0],endKor[1]);
        /*
        var request = {
            origin:start,
            destination:end,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };
        */
        var request = {
                origin: start,
                destination: end,
                waypoints: waypts,
                optimizeWaypoints: true,
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };
        directionsService.route(request, function(response, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
          }
          
        });
      }

      function sendJson() {
          var response = tmpRoute;
		  var dateAndTime = '"' + document.getElementById('date').value + ' ' + document.getElementById('hours').value + ':' + document.getElementById('minutes').value + '"';
		  var freeSeats = '"' + document.getElementById('cars').value + '"';
		  var toleranceDuration = '"' + document.getElementById('toleranceDuration').value + '"';
		  var toleranceDistance = '"' + document.getElementById('toleranceDistance').value + '"';
		  
    	  var start = {
                  "lat": '"'+response.routes[0].legs[0].start_location.lat()+'"',
                  "lng": '"'+response.routes[0].legs[0].start_location.lng()+'"',
                  "address": '"'+response.routes[0].legs[0].start_address+'"'
          };
          var destination = {
                  "lat": '"'+response.routes[0].legs[0].end_location.lat()+'"',
                  "lng": '"'+response.routes[0].legs[0].end_location.lng()+'"',
                  "address": '"'+response.routes[0].legs[0].end_address+'"'
          };
          var duration = '"'+response.routes[0].legs[0].duration.value+'"';
          var distance = '"'+response.routes[0].legs[0].distance.value+'"';
          var stepDistance = 1000;
          var steps = [];
          for(i = 0; i < response.routes[0].legs[0].steps.length; i++) {
          	stepDistance += response.routes[0].legs[0].steps[i].distance.value;
          	if(stepDistance > 1000) {
	            	var step = {
	            	        "lat": '"'+response.routes[0].legs[0].steps[i].end_point.lat()+'"',
	            	        "lng": '"'+response.routes[0].legs[0].steps[i].end_point.lng()+'"'
	            	};
	            	steps.push(step);
	            	stepDistance = 0;
          	}
          }
			
          $.post("ride/saveroute", {
              start: JSON.stringify(start),
              destination: JSON.stringify(destination),
              duration: duration,
              distance: distance,
              stepsData: JSON.stringify(steps),
			  dateAndTime: JSON.stringify(dateAndTime),
			  seats: JSON.stringify(freeSeats),
			  toleranceDuration: JSON.stringify(toleranceDuration),
			  toleranceDistance: JSON.stringify(toleranceDistance)
              });
      }

      function computeTotalDistance(result) {
          var total = 0;
          var myroute = result.routes[0];
          for (i = 0; i < myroute.legs.length; i++) {
            total += myroute.legs[i].distance.value;
          }
          total = total / 1000.
          document.getElementById('total').innerHTML = total + ' km';
       }
      
      function createMarker(map, lat, lng, contentString, title) {

            var myLatlng = new google.maps.LatLng(lat,lng);
            
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: title
            });
            google.maps.event.addListener(marker, 'click', function() {
              infowindow.open(map,marker);
            });
            
            markersArray.push(marker);        
      }
      
      function deleteOverlays() {
    	  if (markersArray) {
    	    for (i in markersArray) {
    	      markersArray[i].setMap(null);
    	    }
    	    markersArray.length = 0;
    	  }
    	}
      
      function addWaypoint(lat, lng) {
    	  	waypts.push({
                  location:lat + "," + lng,
                  stopover:true});
    	  	if(document.getElementById('end').value != '') {
    	  		calcRoute();
    	  	}
      }
      
      function deleteWaypoint(lat, lng) {
    	  if (waypts) {
      	    for (i in waypts) {
      	    	if(waypts[i].location == lat + "," + lng) {
      	    		waypts.splice(i, 1);
      	    	}
      	    }
      	    calcRoute();
    	  }
      }

      window.onload = initialize;