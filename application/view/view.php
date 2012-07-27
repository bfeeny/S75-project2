<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('header-content.php'); ?>
</head>

<body onload="initialize();addPolyline();addMarker();">
    <div class='navbar navbar-fixed-top'>
        <div class='navbar-inner'>
            <div class='container'>
                <a class='brand' href='index.php'>project2</a>

            </div><!--container-->

        </div><!--navbar-inner-->
    </div><!--navbar-->
    <div class='container' style="width:100%; height:100%">
    <?php include('selectbox.php'); ?>            
        <div class="row" style="width:60%; height:80%">
  	        <div class="span8 offset2" id="map_canvas" style="width:60%; height:80%"></div>
        </div>
    </div><!--container-->   
</body>
</html>
