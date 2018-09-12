<?php


include 'All_functions.php';


$module=$_POST['mod'];

if (!empty($module)) {
	
	if (!empty(GetAllRelationDuplicaterecords($module))) {
	  echo GetAllRelationDuplicaterecords($module);
		// echo "Moduli".$module;
	} else {
	  echo "<option value=''>None</option>";
	}
	

} else {
	 echo "<option value=''>None</option>";
}



