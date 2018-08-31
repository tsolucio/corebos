<?php

/**
 * @Author: edmondi kacaj
 * @Date:   2017-12-20 10:43:55
 * @Last Modified by: programim95@gmail.com
 * @Last Modified time: 2018-02-12 14:22:08
 */
include 'All_functions.php';
require_once("modfields.php");


$module=$_POST['mod'];

if (!empty($module)) {
	if (!empty(MappingRelationFields($module))) {
	  	 $showfields="<option values=''>(Select a field)</option>";
		  foreach (MappingRelationFields($module) as $value) {
	          $showfields.=getModFields($value);          
	      }
	      echo $showfields;
		// echo "Moduli".$module;
	} else {
	  echo "<option value=''>None</option>";
	}
	

} else {
	 echo "<option value=''>None</option>";
}
