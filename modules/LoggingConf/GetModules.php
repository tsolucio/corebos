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
 *  Module       : EntittyLog
 *  Version      : 5.4.0
 *  Author       : LoggingConf
 *************************************************************************************************/
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
global $adb;
$action=$_REQUEST['which'];

$result='';
$loggingModules=array();

$query=$adb->pquery("Select distinct(tabid) from vtiger_loggingconfiguration");
$number=$adb->num_rows($query);

$i=0;
while($i<$number)
{
    $loggingModules[]=getTabname($adb->query_result($query,$i));
    $i++;
}

$allModules=array_values(getFieldModuleAccessArray());

if($action!='LoggedModules')
{
$interestModules=array_diff($allModules,$loggingModules);
$j=0;
$result='<tr>';
foreach($interestModules as $module)
{
    $tabid=getTabid($module);
    $result.="<td style=\"width:50%;\" align=\"$align\"><input name='tabids[]' id='tabids[]' value=\"$tabid\" type='checkbox'>".$module."</td>";
    $j++;
    if($j==2)
    {
        $result.="</tr><tr>";
        $j=0;        
    }    
}
}
else{
//$result='<select>';
foreach($loggingModules as $module)
{
    $result.="<option value=\"$module\" > $module</option>";    
}
//$result.='</select>';
}
echo $result;

?>
