/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

google.load("jquery", "1.4.1");

var infobox;
var map;
var detailmap;

function GLoad() {
	var mapLatLng = new google.maps.LatLng(0, 0);
    var mapOptions = {
    	zoom: 2,
    	center: mapLatLng,
    	mapTypeId: google.maps.MapTypeId.HYBRID,
    	mapTypeControlOptions: {  
    	    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  
    	}  
    }
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    var mapbounds = new google.maps.LatLngBounds();

    jQuery.get("/events/mapdata/", {}, function(data) {
    	jQuery(data).find("result").each(function() {
            var result = jQuery(this);
            var venues = result.attr("venues"); 
            if (venues >= 1) {
            	jQuery(data).find("marker").each(function() {
            		var marker = jQuery(this);
            		var htmldata = marker.find("htmldata").text(); 
            		var latlng = new google.maps.LatLng(parseFloat(marker.attr("lat")), parseFloat(marker.attr("lng")));
            		var marker = createMarker(marker.find("name").text(), latlng, marker.find("address").text(), htmldata);
            		mapbounds.extend(latlng); 
            	});
            } else {
            	var boxText = "<div class='fullBox'>";
                boxText += '<h3 style=\"margin: 0px; margin-bottom: 3px; margin-right: 15px;\">No Events To Display</h3>There are currently no events to display.<br/>We are adding new events all the time so please check back soon.';
                boxText += "</div>";
        		var myOptions = {
        			 content: boxText,
        			 boxStyle: {
        			     width: "280px"
        			 },
        			 disableAutoPan: true,
        			 pixelOffset: new google.maps.Size(-140, -40),
        			 position: mapbounds.getCenter(),
        			 closeBoxURL: "",
        			 isHidden: false,
        			 pane: "mapPane",
        			 enableEventPropagation: true
        		};

        		var ibLabel = new InfoBox(myOptions);		
        		ibLabel.open(map);
            }
    	 });
        map.setCenter( mapbounds.getCenter() );
        map.setZoom( map.zoom );
        map.fitBounds(mapbounds);
      });
  }

  function createMarker(name, latlng, address, htmldata) {
    var marker = new google.maps.Marker({
    	position: latlng, 
    	map: map, 
    	title: name,
    	icon: '/_styles/default/images/mapmarker.png'
    });
    var boxText = "<div class='infoBox'>";
    boxText += '<h3 style=\"margin: 0px; margin-bottom: 3px;\">' + name + '</h3><small>' + address + '</small>' + htmldata;
    boxText += "</div>";
    var boxOptions = {
        content: boxText,
        disableAutoPan: false,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(-140, 0),
        zIndex: null,
        boxStyle: { 
            background: "url('/_styles/default/images/tipbox.gif') no-repeat",
            width: "280px"
        },
        closeBoxMargin: "15px 7px 7px 7px",
        closeBoxURL: "/_styles/default/icons/cross.gif",
        infoBoxClearance: new google.maps.Size(18, 18),
        isHidden: false,
        pane: "floatPane",
        enableEventPropagation: true
    };
    google.maps.event.addListener(marker, "click", function() {
      if (infobox) infobox.close();
      infobox = new InfoBox(boxOptions);                
      infobox.open(map, marker);
    });
    return marker;
  }
  google.setOnLoadCallback(GLoad);