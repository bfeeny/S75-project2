<?php
/* Authorization key for BART API */
$bartKey = "EAHR-5KUA-TSME-IADP";

/* URL for BART ETD data */
$etdUrl   = "http://api.bart.gov/api/etd.aspx?cmd=etd&orig={$_GET['stsn']}&key=$bartKey";

/* get raw XML output from BART using cURL */	
$xmlEtdFile   = curlFile($etdUrl);

 /* we transform the raw files into a SimpleXML objects */
$xmlEtdTree   = new SimpleXMLElement($xmlEtdFile);

/* stylize output using a table */
echo "<table style=\"width: 300px;\">";
echo "<tr style=\"text-align: left;\"><th>Destination</th><th>Minutes to Departure</th></tr>";

 /* traverse through $xmlRoutesTree to get all applicable route information to populate routes table */
foreach ($xmlEtdTree->station->etd as $etd) 
{   
	/* show each destination that has departures */      
    echo "<tr style=\"text-align: left;\"><td>$etd->destination</td><td>";
    
    /* show the times of estimated departures */
    foreach($etd->estimate as $estimate)
    {
	    echo $estimate->minutes." ";
    }
    echo "</td></tr>";
}
echo "</table>";

/* Curl routine taken from Extracting XML data in PHP with SimpleXML
   http://www.bobulous.org.uk/coding/php-5-xml-feeds.html
   We will suck down into $xmlRoutesFile the raw XML from BART of their routes */
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
        exit('Failed to open $url.');
    }
}


?>