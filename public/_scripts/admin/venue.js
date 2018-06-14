/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

var geocoder = new google.maps.Geocoder();

function updateMarkerPosition(latLng) {
	dijit.byId('latitude').attr('value',latLng.lat());
	dijit.byId('longitude').attr('value',latLng.lng());
}

function initialize() {
	var latLng = new google.maps.LatLng(slaveLat, slaveLng);
	var map = new google.maps.Map(document.getElementById('mapCanvas'), {
		zoom: 14,
		center: latLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	var marker = new google.maps.Marker({
		position: latLng,
		title: 'Venue Location - Drag to Set',
		map: map,
		draggable: true
	});
  
	// Update current position info.
	updateMarkerPosition(latLng);
	  
	// Add dragging event listeners.
	google.maps.event.addListener(marker, 'dragstart', function() {});
	  
	google.maps.event.addListener(marker, 'drag', function() {
		updateMarkerPosition(marker.getPosition());
	});
	  
	google.maps.event.addListener(marker, 'dragend', function() {});
}

dojo.addOnLoad(function(){
	initialize();
});

