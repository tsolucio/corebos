<?php
/*********************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ********************************************************************************/

/* function used to get the numeration of entities:: autonumeric field
 * return array $entitynum - numeration of entities
 */
function vtws_get_entitynum() {
	require_once 'include/utils/UserInfoUtil.php';
	require_once 'modules/Users/Users.php';
	global $adb, $log;
	$log->debug('Entering vtws_get_entitynum');

	$enumres = $adb->query('SELECT semodule, prefix FROM vtiger_modentity_num');
	$no_of_cont = $adb->num_rows($enumres);
	$entitynum = array();
	for ($i=0; $i<$no_of_cont; $i++) {
		$module = $adb->query_result($enumres, $i, 'semodule');
		$prefix = $adb->query_result($enumres, $i, 'prefix');
		if (is_null($entitynum[$module])) {
			$entitynum[$module] = array($prefix);
		} else {
			$entitynum[$module][] = $prefix;
		}
	}
	$log->debug('Exiting get_entitynum');
	return array($entitynum);
}
?>
