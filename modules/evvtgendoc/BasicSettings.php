<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/BusinessActions/BusinessActions.php';
require_once 'data/CRMEntity.php';
global $app_strings, $mod_strings, $current_language,$currentModule, $theme,$current_user,$log;

function gendoc_changeModuleVisibility($tabid, $status) {
	global $adb;
	$moduleInstance = Vtiger_Module::getInstance($tabid);
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbMap');
	if ($status == 'module_disable') {
		$moduleInstance->deleteLink('DETAILVIEWWIDGET', 'Generate Document');
		$moduleInstance->deleteLink('LISTVIEWBASIC', 'Generate Document');
		$moduleInstance->deleteLink('HEADERSCRIPT', 'Generate Document');
	} else {
		$rs = $adb->pquery(
			'select cbmapid
				from vtiger_cbmap
				inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cbmapid
				where deleted=0 and mapname=?',
			array('GenDocMerge_ConditionExpression')
		);
		if ($rs && $adb->num_rows($rs)>0) {
			$brmap = $adb->query_result($rs, 0, 0);
		} else {
			$brmap = 0;
		}
		BusinessActions::addLink(
			$tabid,
			'DETAILVIEWWIDGET',
			'Generate Document',
			'module=evvtgendoc&action=evvtgendocAjax&file=DetailViewWidget&formodule=$MODULE$&forrecord=$RECORD$',
			'',
			'',
			'',
			true,
			$brmap
		);
		BusinessActions::addLink(
			$tabid,
			'LISTVIEWBASIC',
			'Generate Document',
			"javascript:showgendoctemplates('\$MODULE\$');",
			'',
			'',
			'',
			true,
			$brmap
		);
		$moduleInstance->addLink('HEADERSCRIPT', 'Generate Document', 'modules/evvtgendoc/evvtgendoc.js', 0, '', true);
	}
}
function gendoc_getModuleinfo() {
	global $adb;
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
	$allEntities = array();
	$allModules = array();
	$entityQuery = "SELECT tabid,name FROM vtiger_tab WHERE isentitytype=1 and name NOT IN ('Rss','Recyclebin','Events')";
	$result = $adb->pquery($entityQuery, array());
	while ($result && $row = $adb->fetch_array($result)) {
		$allEntities[$row['tabid']] = getTranslatedString($row['name'], $row['name']);
		$allModules[$row['tabid']] = $row['name'];
	}
	asort($allEntities);
	$mlist = array();
	foreach ($allEntities as $tabid => $mname) {
		$checkres = $adb->pquery(
			'SELECT 1
				FROM vtiger_businessactions
				INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = businessactionsid
				WHERE vtiger_crmentity.deleted = 0
					AND (module_list = ? OR module_list LIKE ? OR module_list LIKE ? OR module_list LIKE ?)
					AND elementtype_action=? AND linklabel=?',
			array($allModules[$tabid], $allModules[$tabid].' %', '% '.$allModules[$tabid].' %', '% '.$allModules[$tabid], 'DETAILVIEWWIDGET', 'Generate Document')
		);
		$mlist[$tabid] = array(
			'name' => $mname,
			'active' => $adb->num_rows($checkres),
		);
	}
	return $mlist;
}

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
if (!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
} else {
	$tabid = isset($_REQUEST['tabid']) ? vtlib_purify($_REQUEST['tabid']) : '';
	$status = isset($_REQUEST['status']) ? vtlib_purify($_REQUEST['status']) : '';
	if ($status != '' && $tabid != '') {
		gendoc_changeModuleVisibility($tabid, $status);
	}
	$infomodules = gendoc_getModuleinfo();
	$smarty->assign('INFOMODULES', $infomodules);
	$smarty->assign('MODULE', $module);
	if (empty($_REQUEST['ajax']) || !$_REQUEST['ajax']) {
		$smarty->display(vtlib_getModuleTemplate($currentModule, 'BasicSettings.tpl'));
	} else {
		$smarty->display(vtlib_getModuleTemplate($currentModule, 'BasicSettingsContents.tpl'));
	}
}
?>
