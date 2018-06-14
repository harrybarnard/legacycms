/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

google.load("maps", "3",  {other_params:"sensor=false"});

  function GLoad() {
    var latlng = new google.maps.LatLng(vlat, vlng);
    var mapOptions = {
      zoom: 13,
      center: latlng,
      draggable: false,
      mapTypeControl: true,
      mapTypeControlOptions: {  
  	    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  
  	  },
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      navigationControl:true,
      navigationControlOptions: google.maps.NavigationControlStyle.SMALL
    };
    var map = new google.maps.Map(document.getElementById("map"), mapOptions);

    var marker = new google.maps.Marker({
        position: latlng, 
        map: map, 
        title: vtitle,
        icon: '/_styles/default/images/mapmarker.png'
    });
  }
  google.setOnLoadCallback(GLoad);