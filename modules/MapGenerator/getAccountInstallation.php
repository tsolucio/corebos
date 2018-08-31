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
global $adb;
$content=array();
$accountId = $_REQUEST['filter']['filters'][0]['value'];
$result = $adb->pquery("SELECT * from  vtiger_accountinstallation  join
                        vtiger_account on vtiger_accountinstallation.linktoacsd = vtiger_account.accountid
                        where accountid = ?",array($accountId));
$num_rows=$adb->num_rows($result);
if($num_rows!=0){
    for($i=0;$i<$num_rows;$i++)
	{

$content[$i]['accInstId'] = $adb->query_result($result,$i,'accountinstallationid');
$content[$i]['accInstValue'] = $adb->query_result($result,$i,'acinstallationname');
}
echo json_encode($content);
}
?>