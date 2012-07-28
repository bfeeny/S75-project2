<?php
/* Start session tracking */
session_start();

$mapLatitude = '37.775362';
$mapLongitude = '-122.417564';

/* Prepare data and make functions available */
include(M . "model.php");

/* if refreshCache is set to true, we repopulate our BART mysql data cache */
if ($refreshCache)
{
	buildCache();
}

/* if we have a route selected, build the script for drawing markers and polylines 
   if the user passed in an invalid route ignore it */
if(isset($_GET['route']) && routeIsValid($_GET['route'])) 
{
	buildMarkerScript($_GET['route']);
	buildPolyLineScript($_GET['route']);
} else 
{
	$markerScript = '';
	$polyLineScript = '';
}

/* include view which handles page formatting */
include(V . "view.php");

?>
