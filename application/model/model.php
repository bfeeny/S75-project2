<?php

/* Authorization key for BART API */
$bartKey = "EAHR-5KUA-TSME-IADP";
$refreshCache = false;
$renderView = '';

/* 
From PHP Tutorials Examples Introduction to PHP PDO 
http://www.phpro.org/tutorials/Introduction-to-PHP-PDO.html#4.3 
connect to our database
*/
function connectDb () 
{
    try 
    {
        return $pdo = new PDO(DSN, DB_USER, DB_PASS);
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }       
}

/*
	Curl routine taken from Extracting XML data in PHP with SimpleXML
	http://www.bobulous.org.uk/coding/php-5-xml-feeds.html
	We will suck down into $xmlRoutesFile the raw XML from BART of their routes
*/
function curlFile($url)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $xml = curl_exec($ch);
    curl_close($ch);
    if($xml)
    {
	    return $xml;
    }
    /* For some reason we were not able to get the raw XML from BART so error */
    else
    {
        exit('Failed to open $xmlRoutesUrl.');
    }
}
/*
we only need to call this function to build our initial cache or optionally refresh it as needed.
This will be controlled with the boolean refreshCache, which will be false by default.
*/
function buildCache()
{
	global $bartKey;
	global $pdo;
	$xmlRoutesUrl   = "http://api.bart.gov/api/route.aspx?cmd=routeinfo&route=all&key=$bartKey";
	$xmlStationsUrl = "http://api.bart.gov/api/stn.aspx?cmd=stns&key=$bartKey";
	
	/* get raw XML output from BART using cURL */	
    $xmlRoutesFile   = curlFile($xmlRoutesUrl);
    $xmlStationsFile = curlFile($xmlStationsUrl);
  
    /* we transform the raw files into a SimpleXML objects */
    $xmlRoutesTree   = new SimpleXMLElement($xmlRoutesFile);
    $xmlStationsTree = new SimpleXMLElement($xmlStationsFile); 
   	
   	$pdo = connectDb();
   	
   	/* query to delete any existing routes, routelist and station data */ 
   	$query1 = "DELETE FROM routes";
   	$query2 = "DELETE FROM routelist";
   	$query3 = "DELETE FROM stations";
   	
    /* below try cause heavily based off Example 1 at http://www.php.net/manual/en/pdo.transactions.php */
    try 
    {  
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        $pdo->exec($query1);
        $pdo->exec($query2);
        $pdo->exec($query3);
        $pdo->commit();
    }           
    catch (Exception $e) 
    {
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
    }

    /* traverse through $xmlRoutesTree to get all applicable route information to populate routes table */
    foreach ($xmlRoutesTree->routes->route as $route) 
    {         
        /* get a sequence of stations for our route to populate our routelist table */
   	    for ($i = 0; $i < $route->num_stns; $i++) 
   	    {
   	        /* setup query with prepare and bind to insure safe input */
   	    	$query1 = "INSERT INTO routelist (number, sequence, station) VALUES(:number,:sequence,:station)";

   	    	try 
            {  
            	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->beginTransaction();
                $stmt = $pdo->prepare($query1);
                $stmt->bindParam(':number', $route->number);
                $stmt->bindValue(':sequence', $i+1);
                $stmt->bindParam(':station', $route->config->station[$i]);
                $stmt->execute();
                $pdo->commit();
            }           
            catch (Exception $e) 
            {
                $pdo->rollBack();
                echo "Failed: " . $e->getMessage();
            }
       	}
       	       	
        /* 
        	build query to populate fresh route information 
        	setup query with prepare and bind to insure safe input 
        */                      
        $query1 = "INSERT INTO routes (number, routeID, abbr, name, color, origin, destination, direction, num_stns) 
        							  VALUES(:number, :routeID, :abbr, :name, :color, :origin, :destination, :direction, :num_stns)";
        							                       
        try 
        {  
        	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($query1);
            $stmt->bindParam(':number', $route->number);
            $stmt->bindParam(':routeID', $route->routeID);
            $stmt->bindParam(':abbr', $route->abbr);
            $stmt->bindParam(':name', $route->name);
            $stmt->bindParam(':color', $route->color);
            $stmt->bindParam(':origin', $route->origin);
            $stmt->bindParam(':destination', $route->destination);
            $stmt->bindParam(':direction', $route->direction);
            $stmt->bindParam(':num_stns', $route->num_stns);
            $stmt->execute();
            $pdo->commit();
        }           
        catch (Exception $e) 
        {
            $pdo->rollBack();
            echo "Failed: " . $e->getMessage();
        }     
    } 
    
    foreach ($xmlStationsTree->stations->station as $station) 
    {           
        /* build query to populate fresh station information.  We use bindParm as some of the data from BART is quoted */
        $query1 = "INSERT INTO stations (name, abbr, gtfs_latitude, gtfs_longitude, address, city, county, state, zipcode) 
        						VALUES(:name, :abbr, :gtfs_latitude, :gtfs_longitude, :address, :city, :county, :state, :zipcode)";
        							                         
        try 
        {  
        	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($query1);
            $stmt->bindParam(':name', $station->name);
            $stmt->bindParam(':abbr', $station->abbr);
            $stmt->bindParam(':gtfs_latitude', $station->gtfs_latitude);
            $stmt->bindParam(':gtfs_longitude', $station->gtfs_longitude);
            $stmt->bindParam(':address', $station->address);
            $stmt->bindParam(':city', $station->city);
            $stmt->bindParam(':county', $station->county);
            $stmt->bindParam(':state', $station->state);
            $stmt->bindParam(':zipcode', $station->zipcode);
            $stmt->execute();
            $pdo->commit();
        }           
        catch (Exception $e) 
        {
            $pdo->rollBack();
            echo "Failed: " . $e->getMessage();
        }
    }
    /* close database and return  */
    $pdo = null;                              
}

function buildMarkerScript($route)
{
		global $markerScript;
		
		$pdo = connectDb();
		
		$query = sprintf("SELECT abbr, name, gtfs_latitude, gtfs_longitude FROM stations JOIN routelist ON routelist.station = stations.abbr 
															 AND routelist.number='%s' ORDER BY routelist.sequence", $route);
		foreach($pdo->query($query) as $row) 
		{
		    $markerScript .= "marker{$row['abbr']} = new google.maps.Marker({
            						position: new google.maps.LatLng({$row['gtfs_latitude']},{$row['gtfs_longitude']}),
            						title: \"{$row['name']}\"
            				  });
             
            				  marker{$row['abbr']}.setMap(map);
            				  ";
            $markerScript .= "google.maps.event.addListener(marker{$row['abbr']}, 'click', function() {
            				    infowindow.open(map,marker{$row['abbr']});
            				  });";

		}
		
		$pdo = null;
}

function buildPolyLineScript($route)
{
	global $polyLineScript;
	
	$pdo = connectDb();
	
	$query = sprintf("SELECT color from routes where number='%s'", $route);
	$color = $pdo->query($query)->fetch()['color'];
   
    $query = sprintf("SELECT gtfs_latitude, gtfs_longitude FROM stations JOIN routelist ON routelist.station = stations.abbr 
															 AND routelist.number='%s' ORDER BY routelist.sequence", $route);

    $polyLineScript = "var polylineCoordinates = [";
    foreach($pdo->query($query) as $row) 
    {
        $polyLineScript .= "new google.maps.LatLng({$row['gtfs_latitude']}, {$row['gtfs_longitude']}),";
    }
        $polyLineScript .= "];
           
                          var polylinePath = new google.maps.Polyline({
                              path: polylineCoordinates,
                              strokeColor: \"$color\",
                              strokeOpacity: 1.0,
                              strokeWeight: 2
                          }); 
       	                  
                          polylinePath.setMap(map);";
    
    $pdo = null;

}
?>
