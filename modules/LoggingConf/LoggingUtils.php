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
function getLoggingModules()
{
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
global $adb;
$loggingModules=array();
$query=$adb->query("Select distinct(tabid) from vtiger_loggingconfiguration");
$number=$adb->num_rows($query);

$i=0;
while($i<$number)
{
    $module=getTabname($adb->query_result($query,$i));
    $loggingModules[$module]=$module;
    $i++;
}
return $loggingModules;
}

function isModuleLog($tabid){
global $adb;
$query=$adb->pquery("Select count(tabid) from vtiger_loggingconfiguration where tabid=?",array($tabid));
$number=$adb->query_result($query,0);
return $number;  
}
function getModuleLogFieldList($tabid)
{
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
global $adb;
$loggingFields=array();
$fields=array();
$query=$adb->pquery("Select fields from vtiger_loggingconfiguration where tabid=? and fields!=''",array($tabid));

$fieldserialized=$adb->query_result($query,0);
$fields=unserialize($fieldserialized);

foreach($fields as $field)
{
    if(is_numeric($field))
    $loggingFields[$field]=$field;
}
return $loggingFields;
}
function getModuleLogFieldListNames($tabid)
{
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
global $adb;
$loggingFields=array();
$loggingFields=getModuleLogFieldList($tabid);
$loggingFieldsNames=array();

foreach($loggingFields as $field)
{
    $query=$adb->pquery("Select columnname from vtiger_field where fieldid=?",array($field));

    $columnname=$adb->query_result($query,0);
    $loggingFieldsNames[]=$columnname;
}
return $loggingFieldsNames;
}

function isLogged($fieldid,$tabid)
{
$allLoggedFields=getModuleLogFieldList($tabid);
return in_array($fieldid,$allLoggedFields);
}
?>
