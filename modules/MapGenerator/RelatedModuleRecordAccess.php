<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

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