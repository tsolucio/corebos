<?php

$dataget=$_POST['nameView'];


if (!empty($dataget)) {
	
	echo CheckName($dataget);

} else {
	echo "";
}

/**
 * this function check if exist in cb_Map the name as you write 
 * @param string $value the name of map you want to check
 * @return string rerturn if find the same name or not
 */
 function CheckName($value='')
{
 	global $log, $mod_strings,$adb;

	if (!empty($value)) {
		
		$sql="SELECT * FROM vtiger_cbmap JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_cbmap.cbmapid WHERE mapname=? AND vtiger_crmentity.deleted=0 ";

		$values=$adb->pquery($sql,array($value));
		$noofrows = $adb->num_rows($values);
		echo $noofrows;
		exit();
		if ($noofrows>0) {
			return "true";
		} else {
			return "false";
		}
		

	} else {

		return "";	  
	}

}
