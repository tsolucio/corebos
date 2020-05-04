<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'include/utils/utils.php';
global $mod_strings,$adb, $current_user;

$step = vtlib_purify($_REQUEST['step']);
$userid = $current_user->id;
switch ($step) {
	case '1':
		$modulename = vtlib_purify($_REQUEST['modulename']);
		$modulelabel = vtlib_purify($_REQUEST['modulelabel']);
		$parentmenu = vtlib_purify($_REQUEST['parentmenu']);
		$ins = $adb->pquery('insert into vtiger_modulebuilder (modulebuilder_name, modulebuilder_label, modulebuilder_parent, status) values(?,?,?,?)', array(
		$modulename,
		$modulelabel,
		$parentmenu,
		'active'));

		$lastINSID = $adb->getLastInsertID();
		$adb->pquery('insert into vtiger_modulebuilder_name (modulebuilderid, date, completed, userid) values (?,?,?,?)', array(
		$lastINSID,
		date('Y-m-d'),
		'20',
		$userid));
		$cookie_name = "moduleid";
		$cookie_value = $lastINSID;
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
		break;
	case '2':
		$moduleid = $_COOKIE['moduleid'];
		foreach ($_REQUEST['blocks'] as $key => $value) {
			if ($value != "") {
				$adb->pquery('insert into vtiger_modulebuilder_blocks (blocks_label, moduleid) values (?,?)', array(
				$value,
				$moduleid
				));
				$adb->pquery('update vtiger_modulebuilder_name SET completed="40" WHERE userid=? AND modulebuilderid=?', array(
				$userid,
				$moduleid,
				));
			}
		}
		break;
	default:
		echo json_encode();
		break;
}
?>