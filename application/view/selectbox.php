<?php

/* Generates a selectbox from cached routes table so that the user can select the route they wish to view */
$pdo = connectDb();
$query = "SELECT number,name FROM routes"; ?>

<!-- learned dropdown auto-submit from http://www.dzone.com/snippets/auto-submit-dropdown-menu -->
<!-- code to keep checkbox selected from 
     http://stackoverflow.com/questions/2904932/how-to-keep-the-selected-value-of-the-select-box-after-form-post-or-get -->
        
<form class="span6 offset3 form-inline" style="width:60%; height:5%">
    <select name="route" onchange='this.form.submit()'>
        <option value="">Select a route...</option>
        <?php
            foreach($pdo->query($query) as $row) 
            {
            	if($row['number'] == $_GET['route'])
            	{
	            	$isSelected = ' selected="selected"'; 
	            } 
	            else 
	            {
		        	$isSelected = ''; 
		        }
            	echo "<option value=\"" . $row['number'] . "\" ". $isSelected .">" . $row['name'] . "</option>";
            }
               $pdo = null;
        ?>
    </select>
</form>
