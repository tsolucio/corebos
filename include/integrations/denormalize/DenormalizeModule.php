<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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

$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';
global $adb;
$module='Messages';
$query='SELECT tablename,entityidfield FROM vtiger_entityname WHERE modulename=?';
$result=$adb->pquery($query, array($module));
$tablename=$adb->query_result($result, 0, 'tablename');
$entityidname=$adb->query_result($result, 0, 'entityidfield');
$join=$tablename.'.'.$entityidname;
$query2= "ALTER TABLE $tablename
	ADD  `crmid` INT( 19 ) NOT NULL DEFAULT 0 ,
	ADD  `cbuuid` char(40) NULL DEFAULT NULL,
	ADD  `smcreatorid` INT( 19 ) NOT NULL DEFAULT 0 ,
	ADD  `smownerid` INT( 19 ) NOT NULL DEFAULT 0 ,
	ADD  `modifiedby` INT( 19 ) NOT NULL DEFAULT 0 ,
	ADD  `createdtime` datetime NULL DEFAULT NULL,
	ADD  `modifiedtime` datetime NULL DEFAULT NULL,
	ADD  `viewedtime` datetime NULL DEFAULT NULL,
	ADD  `setype` varchar(100) NULL DEFAULT NULL,
	ADD  `description` text NULL DEFAULT NULL,
	ADD  `deleted` INT( 1 ) NOT NULL DEFAULT 0,
	ADD INDEX (`crmid`),
	ADD INDEX (`cbuuid`),
	ADD INDEX (`smcreatorid`),
	ADD INDEX (`modifiedby`),
	ADD INDEX (`deleted`),
	ADD INDEX (`smownerid`, `deleted`)";
$result1=$adb->query($query2);
if ($result1) {
	echo "Table ".$tablename." altered with the new crmentity fields.<br>";
}
$updfields = 'update vtiger_field set tablename=? where tabid=? and tablename=?';
$result2=$adb->pquery($updfields, array($tablename, getTabid($module), 'vtiger_crmentity'));
if ($result2) {
	echo "Field meta-data updated.<br>";
}
$query3="UPDATE $tablename inner join vtiger_crmentity on vtiger_crmentity.crmid=$join
	set
	$tablename.crmid = vtiger_crmentity.crmid ,
	$tablename.cbuuid = vtiger_crmentity.cbuuid ,
	$tablename.smcreatorid = vtiger_crmentity.smcreatorid ,
	$tablename.smownerid = vtiger_crmentity.smownerid ,
	$tablename.modifiedby = vtiger_crmentity.modifiedby ,
	$tablename.createdtime = vtiger_crmentity.createdtime ,
	$tablename.modifiedtime = vtiger_crmentity.modifiedtime ,
	$tablename.viewedtime = vtiger_crmentity.viewedtime ,
	$tablename.setype = vtiger_crmentity.setype ,
	$tablename.description= vtiger_crmentity.description,
	$tablename.deleted = vtiger_crmentity.deleted";
$result3=$adb->query($query3);
$que="UPDATE $tablename left join vtiger_crmentity on vtiger_crmentity.crmid=$join set $tablename.deleted=1 WHERE $tablename.createdtime is NUll";
$res=$adb->query($que);
if ($result3) {
	echo "Table ".$tablename." filled with the crmentity data.";
}
/*********************************************************************************
PENDING!!!

Find all places where the vtiger_crmentity fields are used and change them: filters, reports, ....

PENDING!!!
*********************************************************************************/
?>
<br>
Now you have to eliminate the reference to vtiger_crmentity in $tab_name and $tab_name_index and add this code to the main module class:

<pre>
public function __construct() {
	self::$crmentityTable = 'vtiger_messages';
	parent::__construct();
}
</pre>
