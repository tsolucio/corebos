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
 *************************************************************************************************
 *  Module       : Business Mappings:: Import
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Users/Users.php';
include_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';
require_once 'modules/cbMap/processmap/Import.php';

global $adb, $log, $current_user, $current_language;

$current_user = Users::getActiveAdminUser();
if (empty($current_language)) {
	$current_language = $current_user->column_fields['language'];
}

if (isset($argv) && !empty($argv)) {
	$csvfile = $argv[1];
	$mapid = $argv[2];
}
if (!file_exists($csvfile) || !is_readable($csvfile)) {
	echo 'No suitable file specified' . PHP_EOL;
	die;
}

// check that map is correct and load it
if (preg_match('/^[0-9]+x[0-9]+$/', $mapid)) {
	list($cbmapws, $mapid) = explode('x', $mapid);
}
if (is_numeric($mapid)) {
	$cbmap = cbMap::getMapByID($mapid);
} else {
	$cbmapid = GlobalVariable::getVariable('BusinessMapping_'.$mapid, cbMap::getMapIdByName($mapid));
	$cbmap = cbMap::getMapByID($cbmapid);
}
if (empty($cbmap) || $cbmap->column_fields['maptype'] != 'Import') {
	echo 'Invalid Business Map identifier: '. $mapid . PHP_EOL;
}

$cbmap->Import($argv);
?>
