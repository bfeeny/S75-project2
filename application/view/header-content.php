<meta charset="utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"  />
<title>project2</title>
<link rel='stylesheet' type='text/css' href='/bootstrap/css/bootstrap.min.css'/>
<!-- Modify body padding as per http://twitter.github.com/bootstrap/components.html "Fixed navbar" -->
<style type='text/css'>
    html { height: 100% }
    body { padding-top: 60px; padding-bottom: 40px; height: 100%; margin: 0}
    #map_canvas { height: 100% }  
    #select_route {width:20%;height:200px;}
</style>
<link rel='stylesheet' type='text/css' href='/bootstrap/css/bootstrap-responsive.css'/>

<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
    var map = "";
    var marker = "";

    function initialize() {
       
        var mapOptions = {
            center: new google.maps.LatLng(37.775362, -122.417564),
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
            map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
    }


    function addMarker() {
        var infowindow = new google.maps.InfoWindow({
        content: "<h1 style='color:blue'>I am an info window!</h1>", 
        });

        <?php echo $markerScript; ?>
    }
     
    function addPolyline() {
        <?php echo $polyLineScript; ?>
    }
   
  
</script>