<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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
if (isset($_REQUEST['bmapname'])) {
	$bmapname = vtlib_purify($_REQUEST['bmapname']);
} else {
	$bmapname = $currentModule.'_Operation';
}
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $currentModule);
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$map = $cbMap->Operation();
	if (empty($map)) {
		$smarty->assign('showDesert', true);
	} else {
		$cbq = new cbQuestion();
		$filters = $map['filters'];
		$actions = $map['actions'];
		$filterArr = array();
		$actionsArr = array();
		$customlink_params = array(
			'MODULE' => $currentModule,
			'RECORD' => '0x0',
			'ACTION' => vtlib_purify($_REQUEST['action'])
		);
		$tabid = getTabid($currentModule);
		$linksurls = BusinessActions::getAllByType($tabid, array(
			'MASSOPERATIONS'
		), $customlink_params);
		foreach ($filters as $qid) {
			if (!is_numeric($qid)) {
				continue;
			}
			$cbq->retrieve_entity_info($qid, 'cbQuestion');
			$qstatus = $cbq->column_fields['qstatus'];
			if ($qstatus != 'Active') {
				continue;
			}
			$filterArr[] = array(
				'btnName' => $cbq->column_fields['qname'],
				'qmodule' => $cbq->column_fields['qmodule'],
				'record_id' => $cbq->column_fields['record_id']
			);
		}
		foreach ($linksurls['MASSOPERATIONS'] as $url) {
			if (in_array($url->linkid, $actions)) {
				$actionsArr[] = array(
					'btnName' => $url->linklabel,
					'functionName' => $url->linkurl,
				);
			}
		}
	}
	$smarty->assign('filters', $filterArr);
	$smarty->assign('actions', $actionsArr);
	$smarty->assign('moduleView', 'Operation');
	$smarty->assign('moduleShowSearch', false);
	$smarty->assign('showDesert', false);
} else {
	$smarty->assign('showDesert', true);
}
?>