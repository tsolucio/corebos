<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Unified Search Modules Save
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
if (isset($_REQUEST['search_onlyin'])) {
	// Was the search limited by user for specific modules?
	$search_onlyin = vtlib_purify($_REQUEST['search_onlyin']);
	if (!empty($search_onlyin) && $search_onlyin != '--USESELECTED--') {
		$search_onlyin = explode(',', $search_onlyin);
	} elseif ($search_onlyin == '--USESELECTED--') {
		$search_onlyin = $_SESSION['__UnifiedSearch_SelectedModules__'];
	} else {
		$search_onlyin = array();
	}
	if (count($search_onlyin)>0) {
		$search_onlyin = array_filter($search_onlyin, function ($elem) {
			return !(strpos($elem, $GLOBALS['csrf']['input-name']) !== false);
		});
	}
	// Save the selection for future use (UnifiedSearchModules.php)
	coreBOS_Session::set('__UnifiedSearch_SelectedModules__', $search_onlyin);
	if (count($search_onlyin)>0) {
		// we save this users preferences in a global variable
		global $current_user, $adb;
		include_once 'include/Webservices/Create.php';
		$checkrs = $adb->pquery(
			'select crmid
			from vtiger_globalvariable
			inner join vtiger_crmentity on crmid=globalvariableid
			where deleted=0 and gvname=? and smownerid=?',
			array('Application_Global_Search_SelectedModules',$current_user->id)
		);
		if ($adb->num_rows($checkrs)>0) {
			$gvid = $adb->query_result($checkrs, 0, 0);
			$adb->pquery(
				'update vtiger_globalvariable set value=? where globalvariableid=?',
				array(implode(',', $search_onlyin),$gvid)
			);
		} else {
			$wsrs=$adb->pquery('select id from vtiger_ws_entity where name=?', array('Users'));
			if ($wsrs && $adb->num_rows($wsrs)==1) {
				$usrwsid = $adb->query_result($wsrs, 0, 0).'x';
			}
			vtws_create('GlobalVariable', array(
				'gvname' => 'Application_Global_Search_SelectedModules',
				'default_check' => '0',
				'value' => implode(',', $search_onlyin),
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => '',
				'category' => 'System',
				'in_module_list' => '',
				'assigned_user_id' => $usrwsid.$current_user->id,
			), $current_user);
		}
	}
}
?>