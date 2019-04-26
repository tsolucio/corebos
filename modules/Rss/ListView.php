<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'data/Tracker.php';
require_once 'Smarty_setup.php';
require_once 'include/logging.php';
require_once 'include/ListView/ListView.php';
require_once 'include/utils/utils.php';
require_once 'modules/Rss/Rss.php';
global $app_strings, $mod_strings, $currentModule, $theme, $adb;

$current_module_strings = return_module_language($current_language, 'Rss');
$log = LoggerManager::getLogger('rss_list');

$oRss = new vtigerRSS();
if (isset($_REQUEST['folders']) && $_REQUEST['folders'] == 'true') {
	require_once "modules/$currentModule/Forms.php";
	echo get_rssfeeds_form();
	die;
}
if (isset($_REQUEST['record'])) {
	$recordid = vtlib_purify($_REQUEST['record']);
}

$rss_form = new vtigerCRM_Smarty;
$rss_form->assign('MOD', $mod_strings);
$rss_form->assign('APP', $app_strings);
$rss_form->assign('THEME', $theme);
$image_path='themes/'.$theme.'/images/';
$rss_form->assign('IMAGE_PATH', $image_path);
$rss_form->assign('MODULE', $currentModule);
$rss_form->assign('CATEGORY', getParenttab());
$tool_buttons = array(
	'EditView' => 'no',
	'CreateView' => 'no',
	'index' => 'no',
	'Import' => 'no',
	'Export' => 'no',
	'Merge' => 'no',
	'DuplicatesHandling' => 'no',
	'Calendar' => 'no',
	'moduleSettings' => 'no',
);
$rss_form->assign('CHECK', $tool_buttons);
$rss_form->assign('CUSTOM_MODULE', false);
//<<<<<<<<<<<<<<lastrss>>>>>>>>>>>>>>>>>>//
//$url = 'http://forums/rss.php?name=forums&file=rss';
//$url = 'http://forums/weblog_rss.php?w=202';
if (isset($_REQUEST['record'])) {
	$recordid = vtlib_purify($_REQUEST['record']);
	$url = $oRss->getRssUrlfromId($recordid);
	if ($oRss->setRSSUrl($url)) {
		$rss_html = $oRss->getSelectedRssHTML($recordid);
	} else {
		$rss_html = '<strong>'.$mod_strings['LBL_ERROR_MSG'].'</strong>';
	}
	$rss_form->assign('TITLE', gerRssTitle($recordid));
	$rss_form->assign('ID', $recordid);
} else {
	$rss_form->assign('TITLE', gerRssTitle());
	$rss_html = $oRss->getStarredRssHTML();
	$result = $adb->pquery('select rssid from vtiger_rss where starred=1', array());
	$recordid = $adb->query_result($result, 0, 'rssid');
	$rss_form->assign('ID', $recordid);
	$rss_form->assign('DEFAULT', 'yes');
}
if ($currentModule == 'Rss') {
	require_once "modules/$currentModule/Forms.php";
	if (function_exists('get_rssfeeds_form')) {
		$rss_form->assign('RSSFEEDS', get_rssfeeds_form());
	}
}
$rss_form->assign('RSSDETAILS', $rss_html);
//<<<<<<<<<<<<<<lastrss>>>>>>>>>>>>>>>>>>//
if (isset($_REQUEST['directmode']) && $_REQUEST['directmode'] == 'ajax') {
	$rss_form->display('RssFeeds.tpl');
} else {
	$rss_form->display('Rss.tpl');
}
?>
