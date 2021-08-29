<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'modules/Vtiger/KanbanAPI/KanbanUtils.php';

function kbGetBoardItem($module, $record, $boardinfo) {
	$orgtabid = getTabid($module);
	$focus = CRMEntity::getInstance($module);
	$focus->retrieve_entity_info($record, $module);
	if (empty(VTCacheUtils::$_fieldinfo_cache[$orgtabid])) {
		getColumnFields($module);
	}
	$item = array(
		'id' => $module.$record,
		'crmid' => $record,
		'title' => $focus->column_fields[$boardinfo['cards']['title']],
		'lanename' => $focus->column_fields[$boardinfo['lanefield']],
	);
	$fields = array();
	foreach ($boardinfo['cards']['showfields'] as $fname) {
		$finfo = VTCacheUtils::lookupFieldInfo($orgtabid, (string)$fname);
		$fields[] = array(
			'label' => getTranslatedString($finfo['fieldlabel'], $module),
			'value' => $focus->column_fields[$fname],
		);
	}
	$item['showfields'] = $fields;
	$fields = array();
	foreach ($boardinfo['cards']['morefields'] as $fname) {
		$finfo = VTCacheUtils::lookupFieldInfo($orgtabid, (string)$fname);
		$fields[] = array(
			'label' => getTranslatedString($finfo['fieldlabel'], $module),
			'value' => $focus->column_fields[$fname],
		);
	}
	$item['morefields'] = $fields;
	return $item;
}

function kbGetBoardItemFormatted($module, $record, $boardinfo) {
	global $mod_strings, $app_strings;
	$tile = kbGetBoardItem($module, $record, $boardinfo);
	$smarty = new vtigerCRM_Smarty();
	$tabid = getTabid($module);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	$customlink_params = array('MODULE'=>$module, 'RECORD'=>$tile['crmid'], 'ACTION'=>'Kanban');
	$kbadditionalmenu = Vtiger_Link::getAllByType(
		$tabid,
		array('KANBANMENU'),
		$customlink_params,
		null,
		$tile['crmid']
	);
	$smarty->assign(
		'KBMENU_LINKS',
		array_merge(
			getKanbanTileMenu($tabid, $module, $tile['crmid'], $boardinfo['lanenames'], $boardinfo['lanefield'], $tile['lanename'], $boardinfo['kanbanID']),
			$kbadditionalmenu['KANBANMENU']
		)
	);
	$smarty->assign('Tile', $tile);
	return [
		'id' => $tile['id'],
		'lane' => $tile['lanename'],
		'crmid' => $tile['crmid'],
		'kanbanID' => $boardinfo['kanbanID'],
		'title' => $smarty->fetch('Components/Kanban/KanbanTile.tpl'),
	];
}
?>