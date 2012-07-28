<meta charset="utf-8" />
<!-- viewport, map and other google elements setup as explained in Getting Started section of Google Maps API v3
     https://developers.google.com/maps/documentation/javascript/tutorial -->
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"  />
<title>project2</title>
<!-- Twitter bootstrap CSS being used for basic classes http://twitter.github.com/bootstrap/ -->
<link rel='stylesheet' type='text/css' href='/bootstrap/css/bootstrap.min.css'/>
<!-- Modify body padding as per http://twitter.github.com/bootstrap/components.html "Fixed navbar" -->
<style type='text/css'>
    html { height: 100% }
    body { padding-top: 60px; padding-bottom: 40px; height: 100%; margin: 0}
    #map_canvas { height: 100% }  
</style>
<link rel='stylesheet' type='text/css' href='/bootstrap/css/bootstrap-responsive.css'/>
<!-- Load Google Maps v3 API from web https://developers.google.com/maps/documentation/javascript/ -->
<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?sensor=false">
</script>
<!-- Load JQuery API from web http://api.jquery.com/ -->
<script type="text/javascript"
	src="http://code.jquery.com/jquery-latest.js">
</script>

<!-- Basic setup of scripts based on Alain's section examples -->
<script type="text/javascript">
    var map = "";
    var marker = "";
    var infowindow = new google.maps.InfoWindow({
        content: "Loading ETD Info...", 
    });


    function initialize() {     
        var mapOptions = {
            center: new google.maps.LatLng(<?php echo $mapLatitude ?>, <?php echo $mapLongitude ?>),
            zoom: 9,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
            map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
    }

   /* ajax request using jQuery to get ETD data and update infowindow.  Based on lecutre example and an example at
      http://stackoverflow.com/questions/3381700/google-maps-v3-loading-infowindow-content-via-ajax */
   function getETD(marker, id){
        $.ajax({
            url: 'getETD.php',
            data: { stsn: id },
            success: function(data){
                infowindow.setContent(data);
                infowindow.open(map, marker);
            }
        });
    }
    
    /* add markers to our map based on route selected */
    function addMarker() {   
        <?php echo $markerScript; ?>
    }
     
    /* draw polylines on map based on route selected */
    function addPolyline() {
        <?php echo $polyLineScript; ?>
    }
    
</script>