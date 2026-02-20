<?php
/**
 * CmsGoogleMapHelper class
 *
 * Helper for displaying Google Maps using V3 of the Google Maps JS API. Originally based off
 * a helper by marc.fernandezg@gmail.com.
 *
 * @see          https://github.com/marcferna/CakePHP-GoogleMapHelper
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsGoogleMapHelper.html
 * @package		 Cms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsGoogleMapHelper extends AppHelper {

/**
 * Helpers
 */
	public $helpers = array(
		'Html' => array('className' => 'AppHtml')
	);

/**
 * Options for the map() function:
 *
 * - id: map canvas ID
 * - width: default map width
 * - height: default map height
 * - style: map canvas CSS style
 * - zoom: default map zoom level
 * - type: ROADMAP, SATELLITE, HYBRID, TERRAIN 
 * - custom: Any other map option not mentioned before and available for the map, see: https://developers.google.com/maps/documentation/javascript/controls
 * - latitude: Default latitude if the browser doesn't support localization or you don't want localization
 * - longitude: Default longitude if the browser doesn't support localization or you don't want localization
 * - localize: Boolean to localize your position or not
 * - marker: Boolean to put a marker in the position or not
 * - markerTitle: Default marker title (HTML title tag)
 * - markerIcon: Default icon of the marker
 * - markerShadow: default shadow for the marker icon
 * - infoWindow: Boolean to show an information window when you click the marker or not
 * - windowText: Default text inside the information window
 */
	public $mapOptions = array(
		'id' => 'map_canvas',
		'width' => '800px',
		'height' => '800px',
		'style' => 'style',
		'zoom' => 10,
		'type' => 'ROADMAP',
		'custom' => '',
		'latitude' => 48.4395833,
		'longitude' => -123.3932301,
		'localize' => true,
		'marker' => true,
		'markerTitle' => 'My Position',
		'markerIcon' => 'https://vreb.radarhill.ca/img/icons/current.png',
		'markerShadow' => '',
		'infoWindow' => true,
		'windowText' => 'My Position',
		'autoMarker' => true

	);

/**
 * Options for the addMarker() function
 *
 * - infoWindow: Boolean to show an information window when you click the marker or not
 * - windowText: Default text inside the information window
 * - markerTitle: Default marker title (HTML title tag)
 * - markerIcon: Default icon of the marker
 * - markerShadow: Default shadow for the marker icon
 */
	public $markerOptions = array(
		'infoWindow' => true,
		'windowText' => 'Marker info window',
		'markerTitle' => 'Title',
		'markerIcon' => 'https://maps.google.com/mapfiles/marker.png',
		'markerShadow' => 'https://maps.google.com/mapfiles/shadow50.png'
	);

/**
 * Options for the getDirections() function
 *
 * - travelMode: Default travel mode (DRIVING, BICYCLING, TRANSIT, WALKING)
 * - directionsDiv: Div ID to dump the step by step directions
 */
	public $directionsOptions = array(
		'travelMode' => 'DRIVING',
		'directionsDiv' => null
	);

/**
 * Options for addPolyline() function
 *
 * - strokeColor: line color
 * - strokeOpacity: line opacity from 0.1 - 1
 * - strokeWeight: line weight in pixels
 */
	public $polylineOptions = array(
		'strokeColor' => '#FF0000',
		'strokeOpacity' => 1.0,
		'strokeWeight' => 2
	);

/**
 * Method map
 *
 * This method generates a div tag and inserts
 * a google maps.
 *
 * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
 * @param array $options - options array
 * @return string - will returns the map element
 */
	public function map($options = array()) {
	//
		$apiKey = Configure::read('Settings.Site.Google.maps_api_key');
	//
		$url = 'https://maps.googleapis.com/maps/api/js';
	//
		if ($apiKey) {
			$url .= '?key=' . $apiKey;
		}
	//
		$this->Html->script($url, array('once' => true, 'inline' => false));
	//
		$options = array_merge($this->mapOptions, $options);
	//
		extract($options);
	//
		$this->mapId = $id;

		$map = "<div id='$id' style='width:$width; height:$height; $style'></div>";
		
		//put script into the script block
		$mapScript = "
				var latLngs = new Array();
				var markers = new Array();
				var markersIds = new Array();
				var geocoder = new google.maps.Geocoder();

				function geocodeAddress(address, action, map,markerId, markerTitle, markerIcon, markerShadow, windowText, showInfoWindow) {
					geocoder.geocode( { 'address': address}, function(results, status) {
					  if (status == google.maps.GeocoderStatus.OK) {
						if(action =='setCenter'){
							setCenterMap(results[0].geometry.location);
						}
						if(action =='setMarker'){
							//return results[0].geometry.location;
							setMarker(map,markerId,results[0].geometry.location,markerTitle, markerIcon, markerShadow,windowText, showInfoWindow);
						}
						if(action =='addPolyline'){
							return results[0].geometry.location;
						}
					  } else {
						return status;
					  }
					});
				}";
		$mapScript .= "
			var initialLocation;
			var browserSupportFlag =  new Boolean();
			var {$id};
			var myOptions = {
			  zoom: {$zoom},
			  mapTypeId: google.maps.MapTypeId.{$type}
			  " . (($custom != "")? ",$custom" : "") . "

			};
			$(function (){
				{$id} = new google.maps.Map(document.getElementById('$id'), myOptions);
			});
			function setCenterMap(position){
		";

		if ($localize) {
			$mapScript .= "localize();";
		} else {
			$mapScript .= "{$id}.setCenter(position);";
			if (!preg_match('/^https?:\/\//', $markerIcon)) {
				$markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
			}
			if ($marker && $autoMarker) {
				$mapScript .= "setMarker({$id},'center',position,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', ".($infoWindow? 'true' : 'false').");";
			}
		}

		$mapScript .="
			}
		";

		$addMarker = false;
		if (isset($latitude) && isset($longitude)) {
			$addMarker = true;
			$mapScript .="
			$(function (){
				setCenterMap(new google.maps.LatLng({$latitude}, {$longitude}));
			});
			";
		} else if (isset($address)) {
			$addMarker = true;
			$mapScript .="
			$(function (){
				var centerLocation = geocodeAddress('{$address}','setCenter');
			});";
		} else if ($autoMarker) {
			$addMarker = true;
			$mapScript .="
			$(function (){
				setCenterMap(new google.maps.LatLng({$this->defaultLatitude}, {$this->defaultLongitude}));
			});";
		}

		$mapScript .= "
			function localize(){
				if(navigator.geolocation) { // Try W3C Geolocation method (Preferred)
					browserSupportFlag = true;
					navigator.geolocation.getCurrentPosition(function(position) {
					  initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
					  {$id}.setCenter(initialLocation);";
						
		if (!preg_match('/^https?:\/\//', $markerIcon)) {
			$markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
		}
		if ($marker) {
			$mapScript .= "setMarker({$id},'center',initialLocation,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow? 'true' : 'false') . ");";
		}

		$mapScript .= "}, function() {
						  handleNoGeolocation(browserSupportFlag);
						});

				} else if (google.gears) { // Try Google Gears Geolocation
					browserSupportFlag = true;
					var geo = google.gears.factory.create('beta.geolocation');
					geo.getCurrentPosition(function(position) {
						initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
						{$id}.setCenter(initialLocation);";
		if ($marker) {
			$mapScript .= "setMarker({$id},'center',initialLocation,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', ". ($infoWindow? 'true' : 'false') . ");";
		}

		$mapScript .= "}, function() {
					  handleNoGeolocation(browserSupportFlag);
					});
				} else {
					// Browser doesn't support Geolocation
					browserSupportFlag = false;
					handleNoGeolocation(browserSupportFlag);
				}
			}

			function handleNoGeolocation(errorFlag) {
				if (errorFlag == true) {
				  initialLocation = noLocation;
				  contentString = \"Error: The Geolocation service failed.\";
				} else {
				  initialLocation = noLocation;
				  contentString = \"Error: Your browser doesn't support geolocation.\";
				}
				{$id}.setCenter(initialLocation);
				{$id}.setZoom(10);
			}
		";

		if ($addMarker) {
			$mapScript .= "
			function setMarker(map, id, position, title, icon, shadow, content, showInfoWindow){
				var index = markers.length;
				markersIds[markersIds.length] = id;
				markers[index] = new google.maps.Marker({
					position: position,
					map: map,
					icon: icon,
					shadow: shadow,
					title:title
				});
				if(content != '' && showInfoWindow){
					var infowindow = new google.maps.InfoWindow({
						content: content
					});
					google.maps.event.addListener(markers[index], 'click', function() {
						infowindow.open(map,markers[index]);
					});
				}
			 }";
		}
		
		$this->Html->scriptBlock($mapScript, array('inline'=>false));
		
		return $map;
	}

/**
 * Method addMarker
 *
 * This method puts a marker in the google map generated with the method map
 *
 *
 * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
 * @param $mapId - Id that you used to create the map (default 'map_canvas')
 * @param $id - Unique identifier for the marker
 * @param mixed $position - string with the address or an array with latitude and longitude
 * @param array $options - options array
 * @return void
 */
	public function addMarker($mapId, $id, $position, $options = array()) {
		if ($id == null || $mapId == null || $position == null) {
			return null;
		}

		$geolocation = false;

		// Check if position is array and has the two necessary elements
		// or if is not array that the string is not empty
		if (is_array($position)) {
			if (!isset($position["latitude"]) || !isset($position["longitude"])) {
				return null;
			}
			$latitude = $position["latitude"];
			$longitude = $position["longitude"];
		} else {
			$geolocation = true;
		}

		$options = array_merge($this->markerOptions, $options);
		extract($options);

		$marker = "
		$(function(){
		";
		if (!$geolocation) {
			if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitude) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $longitude)) {
				return null;
			}
			if (!preg_match('/^https?:\/\//', $markerIcon)) {
				$markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
			}
			$marker .= "newMarker = new google.maps.LatLng($latitude, $longitude);\n";
			$marker .= "latLngs.push(newMarker);\n";
			$marker .= "setMarker({$mapId},'{$id}',newMarker,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow? 'true' : 'false') . ");\n";
		} else {
			if (empty($position)) {
				return null;
			}
			if (!preg_match('/^https?:\/\//', $markerIcon)) {
				$markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
			}
			$marker .= "geocodeAddress('{$position}', 'setMarker', {$mapId},'{$id}','{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow? 'true' : 'false') . ")";
		}

		$marker .= "
		});
		";
		$this->Html->scriptBlock($marker, array('inline'=>false));
	}

/**
 * Adds JS for centering the map based on multiple markers.
 *
 */
	public function centerOnMarkers() {
		$script = '';
		$script .= "
		$(function(){
			//  Create a new viewpoint bound
			var bounds = new google.maps.LatLngBounds ();
			//  Go through each...
			for (var i = 0, LtLgLen = latLngs.length; i < LtLgLen; i++) {
  				//  And increase the bounds to take this point
  				bounds.extend(latLngs[i]);
			}
			//  Fit these bounds to the map
			" . $this->mapId . ".setCenter(bounds.getCenter());
			" . $this->mapId . ".fitBounds(bounds);
		});
		";
		$this->Html->scriptBlock($script, array('inline'=>false));
		
	}

/**
 * Method getDirections
 *
 * This method gets the direction between two addresses or markers
 *
 *
 * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
 * @param $mapId - Id that you used to create the map (default 'map_canvas')
 * @param $id - Unique identifier for the directions
 * @param mixed $position - array with strings with the from and to addresses or from and to markers
 * @param array $options - options array
 */
	function getDirections($mapId, $id, $position, $options = array()) {
		if ($id == null || $mapId == null || $position == null) {
			return null;
		}

		if (!isset($position["from"]) || !isset($position["to"])) {
			return null;
		}

		$options = array_merge($this->directionsOptions, $options);
		extract($options);

		$directions = "
			  var {$id}Service = new google.maps.DirectionsService();
			  var {$id}Display;
			  {$id}Display = new google.maps.DirectionsRenderer();
			  {$id}Display.setMap({$mapId});
			";
			if ($directionsDiv != null) {
				$directions .= "{$id}Display.setPanel(document.getElementById('{$directionsDiv}'));";
			}

			$directions .= "
			  var request = {
				origin:'{$position["from"]}',
				destination:'{$position["to"]}',
				travelMode: google.maps.TravelMode.{$travelMode}
			  };
			  {$id}Service.route(request, function(result, status) {
				if (status == google.maps.DirectionsStatus.OK) {
				  {$id}Display.setDirections(result);
				}
			  });
		";

		$this->Html->scriptBlock($directions, array('inline'=>false));
	}

/**
 * Method addPolyline
 *
 * This method adds a line between 2 points
 *
 *
 * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
 * @param $mapId - Id that you used to create the map (default 'map_canvas')
 * @param $id - Unique identifier for the directions
 * @param mixed $position - array with start and end latitudes and longitudes
 * @param array $options - options array
 */
	function addPolyline($mapId, $id, $position, $options = array()) {
		if ($id == null || $mapId == null || $position == null) {
			return null;
		}

		if (!isset($position["start"]) || !isset($position["end"])) {
			return null;
		}

		$options = array_merge($this->polylineOptions, $options);
		extract($options);

		// Check if position is array and has the two necessary elements
		if (is_array($position["start"])) {
			if (!isset($position["start"]["latitude"]) || !isset($position["start"]["longitude"])) {
				return null;
			}
			$latitudeStart = $position["start"]["latitude"];
			$longitudeStart = $position["start"]["longitude"];
		}

		if (is_array($position["end"])) {
			if (!isset($position["end"]["latitude"]) || !isset($position["end"]["longitude"])) {
				return null;
			}
			$latitudeEnd = $position["end"]["latitude"];
			$longitudeEnd = $position["end"]["longitude"];
		}

		$polyline = "
		$(function(){
		";

		if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitudeStart) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $longitudeStart)) {
			return null;
		}
		$polyline .= "var start = new google.maps.LatLng({$latitudeStart}, {$longitudeStart}); ";

		if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitudeEnd) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $longitudeEnd)) {
			return null;
		}

		$polyline .= "var end = new google.maps.LatLng({$latitudeEnd}, {$longitudeEnd}); ";

		$polyline .= "
				var poly = [
				start,
				end
			  ];
			  var {$id}Polyline = new google.maps.Polyline({
				path: poly,
				strokeColor: '{$strokeColor}',
				strokeOpacity: {$strokeOpacity},
				strokeWeight: {$strokeWeight}
			  });
			  {$id}Polyline.setMap({$mapId});
		});
		";
	
		$this->Html->scriptBlock($polyline, array('inline'=>false));
	}

/**
 * Method validate latitude
 *
 * @author http://altafphp.blogspot.ca/2013/11/validate-latitude-and-longitude-with.html
 * @param $latitude
 */
 
	function isValidLatitude($latitude) {
		if(preg_match("/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}$/", $latitude)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Method validate longitude
 *
 * @author http://altafphp.blogspot.ca/2013/11/validate-latitude-and-longitude-with.html
 * @param $latitude
 */
	function isValidLongitude($longitude) {
		if(preg_match("/^-?([1]?[1-7][1-9]|[1]?[1-8][0]|[1-9]?[0-9])\.{1}\d{1,6}$/", $longitude)) {
			return true;
		} else {
			return false;
		}
	}
}
