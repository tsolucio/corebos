<?php

//this is for show origin module for Mapping Type 
include 'All_functions.php';
$module=$_POST['mod'];

if (!empty($module)) {
	
	if (!empty(MappingRelationFields($module))) {
		$showfields='<option value="" >(Select a module)</option>';
	   // $showfields="<option values=''>Select one</option>";
	   	  foreach (MappingRelationFields($module) as $value) {
	          if ($value!==$module) {
	               $showfields.='<option value="'.$value.'">'.str_replace("'", "", getTranslatedString($value)).'</option>'; 
	            }        
	      }
	      echo $showfields;
	} else {
	  echo "<option value=''>None</option>";
	}
	

} else {
	 echo "<option value=''>None</option>";
}


