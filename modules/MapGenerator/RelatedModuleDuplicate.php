<?php

/**
 * @Author: edmondi kacaj
 * @Date:   2017-12-19 12:28:29
 * @Last Modified by:   edmondi kacaj
 * @Last Modified time: 2017-12-19 16:47:48
 */
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



