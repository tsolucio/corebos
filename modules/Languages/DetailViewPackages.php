<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaëtan KRONEISEN technique@expert-web.fr
 ********************************************************************************/
require_once('modules/Languages/Config.inc.php');
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');
global $adb;
global $log;
global $mod_strings;
global $app_strings;
global $current_language;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Inside Language Pack View");

$smarty = new vtigerCRM_smarty;
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("UMOD", $mod_strings);
$smod_strings = return_module_language($current_language,'Settings');
$smarty->assign("MOD", $smod_strings);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("IMAGE_PATH", $image_path);

if(isset($_REQUEST['languageid']) && $_REQUEST['languageid']!=''){
	$log->info("The languageid is set");
	$sql = "select * from vtiger_languages where languageid=".$_REQUEST['languageid'];
	$result = $adb->query($sql);
	$languagepackResult = $adb->fetch_array($result);
}

$smarty->assign("LANGUAGESPACKEDITOR",$LanguagesPackEditor);
$smarty->assign("LANGUAGEID", $_REQUEST['languageid']);
$smarty->assign("LANGUAGE", $languagepackResult["language"]);
$smarty->assign("DATECREATION", $languagepackResult["createddate"]);
$smarty->assign("LASTCHANGE", $languagepackResult["modifiedtime"]);
$smarty->assign("PREFIX", $languagepackResult["prefix"]);
$smarty->assign("ENCODING", $languagepackResult["encoding"]);
$smarty->assign("VERSION", $languagepackResult["version"]);
$smarty->assign("AUTHOR", $languagepackResult["author"]);
$smarty->assign("LICENSE", nl2br($languagepackResult["license"]));
$smarty->assign("LOCKFOR", $languagepackResult["lockfor"]);

$smarty->display("Settings/Languages/LanguagePackDetailView.tpl");
?>