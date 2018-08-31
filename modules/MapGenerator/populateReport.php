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
global $log,$adb;

$accInstallation = $_REQUEST['installationID'];
//get parameters of installation
$accQuery=$adb->pquery("select * from vtiger_accountinstallation
                       where accountinstallationid=?",array($accInstallation));

$dbname = $adb->query_result($accQuery,0,"dbname");
$acno = $adb->query_result($accQuery,0,"acin_no");

$content=array();
$query="SELECT reportname,reportid,queryid from $acno$dbname.vtiger_report";
$result = $adb->query($query);
$num_rows=$adb->num_rows($result);
if($num_rows!=0){
for($i=0;$i<=$num_rows;$i++)
{
//$content[$i]['reportId'] = $adb->query_result($result,$i,'reportid');
 $reportId = $adb->query_result($result,$i,'reportid');
//$content[$i]['reportValue'] = getTranslatedString($adb->query_result($result,$i,'reportname')); 
 $reportValue = getTranslatedString($adb->query_result($result,$i,'reportname'));
 if((isset($_REQUEST['selectedview']) && $_REQUEST['selectedview'] == "clientreport") || (isset($_REQUEST['selectedview']) && $_REQUEST['selectedview'] == "clientreport2"))
  $res.='<option value="'.$reportId.'">'.$reportId.'_'.$reportValue.'</option>';
 else $res.='<option value="'.$reportId.'">'.$reportValue.'</option>';
}
//echo json_encode($content);
echo $res;
}
?>