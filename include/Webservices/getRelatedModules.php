<?php
/***********************************************************************************
 * Copyright 2012-2018 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
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
 ************************************************************************************/

function getRelatedModulesInfomation($module, $user) {
	include_once 'include/Webservices/GetFilterFields.php';
	global $log, $adb;
	$log->debug('Entering getRelatedModulesInfomation(' . $module . ') method ...');
	$types = vtws_listtypes(null, $user);
	if (!in_array($module, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}
	$cur_tab_id = getTabid($module);
	$result = $adb->pquery('select * from vtiger_relatedlists where tabid=?', array($cur_tab_id));
	$num_row = $adb->num_rows($result);
	$focus_list = array();
	for ($i = 0; $i < $num_row; $i++) {
		$rel_tab_id = $adb->query_result($result, $i, 'related_tabid');
		$label = $adb->query_result($result, $i, 'label');
		$actions = $adb->query_result($result, $i, 'actions');
		$relationId = $adb->query_result($result, $i, 'relation_id');
		if ($rel_tab_id != 0) {
			$relModuleName = getTabModuleName($rel_tab_id);
			if (!in_array($relModuleName, $types['types'])) {
				continue;
			}
			$focus_list[$label] = array(
				'related_tabid' => $rel_tab_id,
				'related_module' => $relModuleName,
				'label'=> $label,
				'labeli18n' =>getTranslatedString($label, $relModuleName),
				'actions' => $actions,
				'relationId' => $relationId,
				'filterFields'=> vtws_getfilterfields($relModuleName, $user),
			);
		} else {
			$focus_list[$label] = array(
				'related_tabid' => $rel_tab_id,
				'related_module' => '',
				'label'=> $label,
				'labeli18n' =>getTranslatedString($label, $module),
				'actions' => $actions,
				'relationId' => $relationId,
				'filterFields'=> vtws_getfilterfields($module, $user),
			);
		}
	}
	return $focus_list;
}
?>