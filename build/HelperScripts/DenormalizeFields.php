<?php

$Vtiger_Utils_Log = true;

include_once('config.inc.php');
include_once('include/utils/utils.php');
global $adb;
$module='Accounts';
$query="SELECT tablename,entityidfield FROM vtiger_entityname WHERE modulename=?";
$result=$adb->pquery($query,array($module));
$tablename=$adb->query_result($result,0,'tablename');
$entityidname=$adb->query_result($result,0,'entityidfield');
$join=$tablename.'.'.$entityidname;
$query2= "ALTER TABLE  $tablename
          ADD  `mycreatorid` INT( 19 ) NOT NULL DEFAULT 0 ,
          ADD  `myownerid` INT( 19 ) NOT NULL DEFAULT 0 ,
          ADD  `mymodifierid` INT( 19 ) NOT NULL DEFAULT 0 ,
          ADD  `mycreatedtime` datetime NULL DEFAULT NULL,
          ADD  `mymodifiedtime` datetime NULL DEFAULT NULL,
          ADD  `mydescription`  text NULL DEFAULT NULL,
          ADD  `mydeleted` INT( 1 ) NOT NULL DEFAULT 0";
$result2=$adb->query($query2);
if($result2){
  echo "Table ".$tablename." altered with the new crmentity fields.<br>";
}
$query3="UPDATE $tablename inner join vtiger_crmentity on vtiger_crmentity.crmid=$join
         set
        `mycreatorid` = smcreatorid ,
        `myownerid` = smownerid ,
        `mymodifierid` = modifiedby ,
        `mycreatedtime` = createdtime ,
        `mymodifiedtime` = modifiedtime ,
        `description`= mydescription,
        `mydeleted` = deleted";
$result3=$adb->query($query3);
$que="UPDATE $tablename left join vtiger_crmentity on vtiger_crmentity.crmid=$join
      set `mydeleted`=1 WHERE $tablename.mycreatedtime is NUll";
$res=$adb->query($que);
if($result3) {
  echo "Table ".$tablename." filled with the crmentity data.";
}
