

function initMap() {
   
  var mapOptions = {
    zoom: 18
  };

  map = new google.maps.Map(document.getElementById('address-map'),
      mapOptions);

  // Try HTML5 geolocation to get location
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
       var c_latitude  = document.getElementById("location_lattitude").value;   
       var c_longitude = document.getElementById("location_longitude").value;

       if(c_latitude != null && c_longitude != null){

         var pos = new google.maps.LatLng(c_latitude,c_longitude);

       }else{

          var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);
       }
      

       marker = new google.maps.Marker({
        map: map,
        position: pos,
      });

  //gets the current latlong coordinate
       
      map.setCenter(pos);

    }, function() {
      handleNoGeolocation(true);
    });
  } else {
    // Browser doesn't support Geolocation
    handleNoGeolocation(false);
  }


  //if it all fails
    function handleNoGeolocation(errorFlag) {
      if (errorFlag) {
        var content = 'Error: The Geolocation service failed.';
      } else {
        var content = 'Error: Your browser doesn\'t support geolocation.';
      }

      var options = {
        map: map,
        position: new google.maps.LatLng(60, 105),
      };

      var marker = new google.maps.Marker(options);
      map.setCenter(options.position);


    }

    google.maps.event.addDomListener(window, 'load', initMap);

    google.maps.event.addListener(map, 'click', function(event) {

       placeMarker(event.latLng);

    });

    function placeMarker(location) {

     if (marker == null){

           marker = new google.maps.Marker({
              position: location,
              map: map
          }); 


        } else {   
            marker.setPosition(location); 
        } 
      document.getElementById("location_lattitude").value=marker.getPosition().lat();   
      document.getElementById("location_longitude").value=marker.getPosition().lng();
    }

}

