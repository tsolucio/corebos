<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
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
