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
function vtws_get_entitynum($user = '') {
	global $adb;
	$types = vtws_listtypes(null, $user);
	$enumres = $adb->pquery(
		'SELECT semodule, prefix FROM vtiger_modentity_num WHERE active=1 and semodule in ('. generateQuestionMarks($types['types']) .')',
		$types['types']
	);
	$entitynum = array();
	while ($en=$adb->fetch_array($enumres)) {
		if (empty($entitynum[$en['semodule']])) {
			$entitynum[$en['semodule']] = array($en['prefix']);
		} else {
			$entitynum[$en['semodule']][] = $en['prefix'];
		}
	}
	return array($entitynum);
}
?>
