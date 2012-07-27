<?php
/* Start session tracking */
session_start();

/* Prepare data and make functions available */
include(M . "model.php");

if ($refreshCache)
{
	buildCache();
}

if(isset($_GET['route'])) 
{
	echo "Route " . $_GET['route'] . " has been chosen";
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
