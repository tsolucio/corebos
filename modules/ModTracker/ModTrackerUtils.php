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
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'vtlib/Vtiger/Module.php';
require_once __DIR__ .'/ModTracker.php';

class ModTrackerUtils {

	public static function modTrac_changeModuleVisibility($tabid, $status) {
		if ($status == 'module_disable') {
			ModTracker::disableTrackingForModule($tabid);
		} else {
			ModTracker::enableTrackingForModule($tabid);
		}
	}
	public static function modTrac_getModuleinfo() {
		global $adb;
		$query = $adb->pquery(
			"SELECT vtiger_modtracker_tabs.visible,vtiger_tab.name,vtiger_tab.tabid
				FROM vtiger_tab
				LEFT JOIN vtiger_modtracker_tabs ON vtiger_modtracker_tabs.tabid = vtiger_tab.tabid
				WHERE vtiger_tab.isentitytype = 1 AND vtiger_tab.name NOT IN('Emails', 'Rss','Recyclebin','Events','Calendar')",
			array()
		);
		$rows = $adb->num_rows($query);

		for ($i = 0; $i < $rows; $i++) {
			$infomodules[$i]['tabid']  = $adb->query_result($query, $i, 'tabid');
			$infomodules[$i]['visible']  = $adb->query_result($query, $i, 'visible');
			$infomodules[$i]['name'] = $adb->query_result($query, $i, 'name');
		}
		usort($infomodules, function ($a, $b) {
			return (strtolower(getTranslatedString($a['name'], $a['name'])) < strtolower(getTranslatedString($b['name'], $b['name']))) ? -1 : 1;
		});
		return $infomodules;
	}
}
?>
