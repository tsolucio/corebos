<?php

/**
 * @Author: edmondi kacaj
 * @Date:   2017-12-13 12:40:47
 * @Last Modified by:   edmondi kacaj
 * @Last Modified time: 2017-12-13 17:06:32
 */


include 'All_functions.php';


$module=$_POST['mod'];

if (!empty($module)) {
	
	if (!empty(GetAllrelation($module))) {
	  echo GetAllrelation($module);
		// echo "Moduli".$module;
	} else {
	  echo "<option value=''>None</option>";
	}
	

} else {
	 echo "<option value=''>None</option>";
}



/**
 * Gets the allrelation.
 *
 * @param      string  $module  The module
 *
 * @return     string  The allrelation.
 */
function GetAllrelation($module="")
{
	global $adb, $root_directory, $log;
	if (!empty($module))
	{
		$log->debug("Info!! Value is not ampty");
		$idmodul=getModuleID($module,"tabid");
		$sql="SELECT * from vtiger_relatedlists where tabid='$idmodul'";
		$result = $adb->query($sql);
	    $num_rows=$adb->num_rows($result);
	    $historymap="";
	    $a='<option value="" >(Select a module)</option>';
	    if($num_rows!=0)
	    {
	        for($i=1;$i<=$num_rows;$i++)
	        {
	            $Modules = $adb->query_result($result,$i-1,'label');
	           
	            $a.='<option value="'.$Modules.'">'.str_replace("'", "", getTranslatedString($Modules)).'</option>';	            
	        }
	       return $a;
	    }else{$log->debug("Info!! The database is empty or something was wrong");}
    }else {
		return "";
	}
	 
}