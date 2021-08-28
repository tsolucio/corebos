<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
include_once 'modules/Vtiger/KanbanViewMenu.php';
$bmapname = $currentModule.'_Kanban';
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $currentModule);
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$cbMapKb = $cbMap->Kanban();
}
if (empty($cbMapKb)) {
	$smarty->assign('showDesert', true);
} else {
	$smarty->assign('showDesert', false);
	$smarty->assign('kanbanID', uniqid(strtolower($currentModule)));
	$smarty->assign('moduleShowSearch', $cbMapKb['showsearch']);
	$smarty->assign('moduleShowFilter', $cbMapKb['showfilter']);
	$smarty->assign('kbLanes', $cbMapKb['lanes']);
	$tile =array(
		'id' => $currentModule.'crmid',
		'title' => 'ChemexChemex ChemexChemex ChemexChemex',
		'showfields' => array(
			array(
				'label' => 'label1',
				'value' => 'value1',
			),
			array(
				'label' => 'label2',
				'value' => 'value2',
			),
		),
		'morefields' => array(
			array(
				'label' => 'mlabel1',
				'value' => 'mvalue1',
			),
			array(
				'label' => 'mlabel2 mlabel2',
				'value' => 'mlabel2 mlabel2mlabel2 mlabel2mlabel2 mlabel2mlabel2 mlabel2mlabel2 mlabel2mvalue2',
			),
		),
	);
	$smarty->assign('Tile', $tile);
	include_once 'vtlib/Vtiger/Link.php';
	$customlink_params = array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>'Kanban');
	$tabid = getTabid($currentModule);
	$kbadditionalmenu = Vtiger_Link::getAllByType(
		$tabid,
		array('KANBANMENU'),
		$customlink_params,
		null,
		$focus->id
	);
	$smarty->assign('KBMENU_LINKS', array_merge(getKanbanTileMenu($tabid, $currentModule, 74, $cbMapKb['lanenames'], $cbMapKb['lanefield']), $kbadditionalmenu['KANBANMENU']));
}
$smarty->assign('moduleView', 'Kanban');
?>