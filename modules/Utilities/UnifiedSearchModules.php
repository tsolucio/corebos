<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

global $app_strings, $mod_strings, $current_language, $currentModule, $current_user, $theme, $adb;

$selected_modules = array();
if (!empty($_SESSION['__UnifiedSearch_SelectedModules__']) && is_array($_SESSION['__UnifiedSearch_SelectedModules__'])) {
	$selected_modules = $_SESSION['__UnifiedSearch_SelectedModules__'];
} else {
	$appSearchModules = GlobalVariable::getVariable('Application_Global_Search_SelectedModules', '');
	if (!empty($appSearchModules)) {
		$selected_modules = explode(',', $appSearchModules);
		coreBOS_Session::set('__UnifiedSearch_SelectedModules__', $selected_modules);
	}
}

$doNotSearchThese = array('Dashboard','Home','Calendar','Events','Rss','Reports','Portal','Users','ConfigEditor','Import','MailManager','Mobile','ModTracker',
	'PBXManager','VtigerBackup','WSAPP','cbupdater','CronTasks','RecycleBin','Tooltip','Webforms','Calendar4You','GlobalVariable','cbMap','evvtMenu','cbAuditTrail',
	'cbLoginHistory','cbtranslation');
$doNotSearchTheseTabids = array();
foreach ($doNotSearchThese as $mname) {
	$tabid = getTabid($mname);
	if (!empty($tabid)) {
		$doNotSearchTheseTabids[] = $tabid;
	}
}
$allowed_modules = array();
$sql = 'select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
	where vtiger_tab.tabid not in ('.generateQuestionMarks($doNotSearchTheseTabids).') and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2)';
$moduleres = $adb->pquery($sql, array($doNotSearchTheseTabids));
while ($modulerow = $adb->fetch_array($moduleres)) {
	if (is_admin($current_user) || isPermitted($modulerow['name'], 'DetailView') == 'yes') {
		$modulename = $modulerow['name'];
		$allowed_modules[$modulename] = array(
			'label' => getTranslatedString($modulename, $modulename),
			'selected' => in_array($modulename, $selected_modules)
		);
	}
}
uasort($allowed_modules, function ($a, $b) {
	return (strtolower($a['label']) < strtolower($b['label'])) ? -1 : 1;
});

require_once 'Smarty_setup.php';

$smarty = new vtigerCRM_Smarty();
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ALLOWED_MODULES', $allowed_modules);

$smarty->display('UnifiedSearchModules.tpl');
?>