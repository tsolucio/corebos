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

function kbGetBoardItems($module, $limit_start_rec, $boardinfo) {
	global $adb;
	$items = array();
	$sql = kbGetItemQuery($module, $limit_start_rec, $boardinfo);
	if ($sql!='') {
		try {
			$rs = $adb->query($sql);
		} catch (Exception $e) {
			return $items;
		}
		if ($rs && $adb->num_rows($rs)) {
			$orgtabid = getTabid($module);
			$focus = CRMEntity::getInstance($module);
			if (empty(VTCacheUtils::$_fieldinfo_cache[$orgtabid])) {
				getColumnFields($module);
			}
			foreach ($adb->rowGenerator($rs) as $row) {
				$item = array(
					'id' => $module.$row[$focus->table_index],
					'crmid' => $row[$focus->table_index],
					'title' => $row[$boardinfo['cards']['title']],
				);
				$fields = array();
				foreach ($boardinfo['cards']['showfields'] as $fname) {
					$finfo = VTCacheUtils::lookupFieldInfo($orgtabid, (string)$fname);
					$fields[] = array(
						'label' => getTranslatedString($finfo['fieldlabel'], $module),
						'value' => $row[$fname],
					);
				}
				$item['showfields'] = $fields;
				$fields = array();
				foreach ($boardinfo['cards']['morefields'] as $fname) {
					$finfo = VTCacheUtils::lookupFieldInfo($orgtabid, (string)$fname);
					$fields[] = array(
						'label' => getTranslatedString($finfo['fieldlabel'], $module),
						'value' => $row[$fname],
					);
				}
				$item['morefields'] = $fields;
				$items[] = $item;
			}
		}
	}
	return $items;
}

function kbGetBoardItemsFormatted($module, $limit_start_rec, $boardinfo) {
	global $mod_strings, $app_strings;
	$items = kbGetBoardItems($module, $limit_start_rec, $boardinfo);
	$ret = array();
	$smarty = new vtigerCRM_Smarty();
	$tabid = getTabid($module);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	foreach ($items as $tile) {
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
				getKanbanTileMenu($tabid, $module, $tile['crmid'], $boardinfo['lanenames'], $boardinfo['lanefield'], $boardinfo['lanename'], $boardinfo['kanbanID']),
				$kbadditionalmenu['KANBANMENU']
			)
		);
		$smarty->assign('Tile', $tile);
		$ret[] = [
			'id' => $tile['id'],
			'crmid' => $tile['crmid'],
			'lane' => $boardinfo['lanename'],
			'kanbanID' => $boardinfo['kanbanID'],
			'title' => $smarty->fetch('Components/Kanban/KanbanTile.tpl'),
		];
	}
	return $ret;
}
?>