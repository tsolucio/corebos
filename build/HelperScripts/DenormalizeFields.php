<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

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
          ADD  `mydeleted` INT( 1 ) NOT NULL DEFAULT 0,
          ADD INDEX (`mycreatorid`),
          ADD INDEX (`mymodifierid`),
          ADD INDEX (`mydeleted`),
          ADD INDEX (`myownerid`, `mydeleted`)";
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
        `mydescription`= description,
        `mydeleted` = deleted";
$result3=$adb->query($query3);
$que="UPDATE $tablename left join vtiger_crmentity on vtiger_crmentity.crmid=$join
      set `mydeleted`=1 WHERE $tablename.mycreatedtime is NUll";
$res=$adb->query($que);
if($result3) {
  echo "Table ".$tablename." filled with the crmentity data.";
}
