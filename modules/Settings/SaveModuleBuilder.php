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
		$ins = $adb->pquery('INSERT INTO vtiger_modulebuilder (modulebuilder_name, modulebuilder_label, modulebuilder_parent, status) VALUES(?,?,?,?)', array(
		$modulename,
		$modulelabel,
		$parentmenu,
		'active'));

		$lastINSID = $adb->getLastInsertID();
		$adb->pquery('INSERT INTO vtiger_modulebuilder_name (modulebuilderid, date, completed, userid) VALUES (?,?,?,?)', array(
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
				$adb->pquery('INSERT INTO vtiger_modulebuilder_blocks (blocks_label, moduleid) VALUES (?,?)', array(
				$value,
				$moduleid
				));
				$adb->pquery('UPDATE vtiger_modulebuilder_name SET completed="40" WHERE userid=? AND modulebuilderid=?', array(
				$userid,
				$moduleid,
				));
			}
		}
		break;
	case '3':
		if (isset($_REQUEST['fields'])) {
			$moduleid = $_COOKIE['moduleid'];
			//get Module Name
			$moduleSql = $adb->pquery('SELECT modulebuilder_name FROM vtiger_modulebuilder WHERE modulebuilderid=?', array($moduleid));
			$moduleName = $adb->query_result($moduleSql, 0, 0);
			$fields = vtlib_purify($_REQUEST['fields']);
			$adb->pquery('INSERT INTO vtiger_modulebuilder_fields (blockid, moduleid,fieldname,uitype,columnname,tablename,generatedtype,fieldlabel,readonly,presence,sequence,maximumlength,typeofdata,quickcreate,displaytype,masseditable,entityidentifier,entityidfield,entityidcolumn,relatedmodules) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', array(
				$fields[0]['blockid'],
				$moduleid,
				$fields[0]['fieldname'],
				$fields[0]['uitype'],
				$fields[0]['columnname'],
				strtolower('vtiger_'.$moduleName),
				$fields[0]['generatedtype'],
				$fields[0]['fieldlabel'],
				$fields[0]['readonly'],
				$fields[0]['presence'],
				$fields[0]['sequence'],
				$fields[0]['maximumlength'],
				$fields[0]['typeofdata'],
				$fields[0]['quickcreate'],
				$fields[0]['displaytype'],
				$fields[0]['masseditable'],
				$fields[0]['entityidentifier'],
				$fields[0]['entityidfield'],
				$fields[0]['entityidcolumn'],
				$fields[0]['relatedmodules'],
			));
			$adb->pquery('UPDATE vtiger_modulebuilder_name SET completed="60" WHERE userid=? AND modulebuilderid=?', array(
			$userid,
			$moduleid,
			));
		}
		break;
	case '4':
		$moduleid = $_COOKIE['moduleid'];
		$customview = vtlib_purify($_REQUEST['customview']);
		foreach ($customview as $key => $value) {
			$viewname = $value['viewname'];
			$setdefault = (String)$value['setdefault'];
			$fields = json_encode($value['fields']['fieldObj']);
			$setmetrics = 'false';
			$adb->pquery('INSERT INTO vtiger_modulebuilder_customview (viewname, setdefault, setmetrics, fields, moduleid) VALUES(?,?,?,?,?)', array(
				$viewname,
				$setdefault,
				$setmetrics,
				$fields,
				$moduleid
			));
		}
		break;
	default:
		echo json_encode();
		break;
}
?>