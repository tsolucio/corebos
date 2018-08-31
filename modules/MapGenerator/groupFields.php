<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
global $log;
require_once ('include/utils/utils.php');
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
//require_once('database/DatabaseConnection.php');
require_once ('include/CustomFieldUtil.php');
require_once('config.inc.php');
require_once ('data/Tracker.php');
$dbname = $dbconfig['db_name'];
$content=array();
$reportId = $_POST['reportId'];
$result = $adb->pquery("SELECT * from  $dbname.vtiger_selectcolumn 
                        where queryid = ? order by columnindex",array($reportId));
$num_rows=$adb->num_rows($result);
if($num_rows!=0){
    for($i=0;$i<=$num_rows;$i++)
	{
            if($adb->query_result($result,$i,'columnname')!='none'){
                $cn=explode(":",$adb->query_result($result,$i,'columnname'));
		$f = getTranslatedString($cn[2]);
                $index = $adb->query_result($result,$i,'columnindex');
                $id =$cn[0].'.'.$cn[1];
                $f1=str_replace("_"," ",utf8_encode(html_entity_decode($f)));
                $n++;

$content[$i]['value'] = $adb->query_result($result,$i,'fieldname'.$i);
$content[$i]['text'] = getTranslatedString($adb->query_result($result,$i,$id.'|'.$index));
}
}
}
?>
