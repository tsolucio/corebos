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
	global $adb;
	$types = vtws_checkListTypesPermission($module, $user);

	$cur_tab_id = getTabid($module);
	$result = $adb->pquery('select * from vtiger_relatedlists where tabid=?', array($cur_tab_id));
	$num_row = $adb->num_rows($result);
	$focus_list = array();
	for ($i = 0; $i < $num_row; $i++) {
		$rel_tab_id = $adb->query_result($result, $i, 'related_tabid');
		$label = $adb->query_result($result, $i, 'label');
		$actions = $adb->query_result($result, $i, 'actions');
		$relationId = $adb->query_result($result, $i, 'relation_id');
		$relationfieldid = $adb->query_result($result, $i, 'relationfieldid');
		if (empty($relationfieldid)) {
			$relationfield = null;
		} else {
			$rs = $adb->pquery('select fieldname from vtiger_field where fieldid=?', array($relationfieldid));
			$relationfield = $adb->query_result($rs, 0, 'fieldname');
		}
		if ($rel_tab_id != 0) {
			$relModuleName = getTabModuleName($rel_tab_id);
			if (!in_array($relModuleName, $types['types'])) {
				continue;
			}
			$ffields = vtws_getfilterfields($relModuleName, $user);
			$bmapname = $relModuleName.'_ListColumns';
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$cbMapLC = $cbMap->ListColumns();
				$ffields['fields'] = array_values($cbMapLC->getListFieldsNameFor($module));
				$ffields['linkfields'] = $cbMapLC->getListLinkFor($module);
			}
			$focus_list[$label] = array(
				'related_tabid' => $rel_tab_id,
				'related_module' => $relModuleName,
				'label'=> $label,
				'labeli18n' =>getTranslatedString($label, $relModuleName),
				'actions' => $actions,
				'relationId' => $relationId,
				'relatedfield' => $relationfield,
				'relationtype' => $adb->query_result($result, $i, 'relationtype'),
				'filterFields'=> $ffields,
			);
		} else {
			$focus_list[$label] = array(
				'related_tabid' => $rel_tab_id,
				'related_module' => '',
				'label'=> $label,
				'labeli18n' =>getTranslatedString($label, $module),
				'actions' => $actions,
				'relationId' => $relationId,
				'relatedfield' => $relationfield,
				'relationtype' => $adb->query_result($result, $i, 'relationtype'),
				'filterFields'=> vtws_getfilterfields($module, $user),
			);
		}
	}
	return $focus_list;
}
?>