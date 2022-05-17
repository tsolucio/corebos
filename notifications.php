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
 *  Module       : Notifications Drivers
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/database/PearDatabase.php';
include_once 'include/utils/utils.php';
$adminid = Users::getActiveAdminId();
if (GlobalVariable::getVariable('Notifications_Enabled', 1, 'Users', $adminid)==0) {
	exit;
}
checkFileAccessForInclusion("include/language/$default_language.lang.php");
require_once "include/language/$default_language.lang.php";
global $adb, $current_language;
$current_language = $default_language;
$type = vtlib_purify($_REQUEST['type']);
$driver = $adb->pquery('select path, functionname ,signedkey, signedvalue,signedvalidation from vtiger_notificationdrivers where type=?', array($type));
if ($driver && $adb->num_rows($driver)>0) {
	$path = $adb->query_result($driver, 0, 'path');
	$function = $adb->query_result($driver, 0, 'functionname');
	$signedkey = $adb->query_result($driver, 0, 'signedkey');
	$signedvalue = $adb->query_result($driver, 0, 'signedvalue');
	if ($type == 'googlecal' || $type == 'googlestorage') {
		$input = $_GET['code'];
	} else {
		$input = file_get_contents('php://input');
	}
	if (!empty($signedkey) && !empty($signedvalue)) {
		$signedfunction = $adb->query_result($driver, 0, 'signedvalidation');
		if (empty($signedfunction)) {
			if (empty($_REQUEST[$signedvalue]) || $_REQUEST[$signedvalue]!=$signedkey) {
				die();
			}
		} else {
			if (!$signedfunction($signedvalue, $signedkey, $input)) {
				die();
			}
		}
	}
	//run function
	include_once $path;
	$function($input);
}
