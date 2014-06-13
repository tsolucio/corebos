<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaëtan KRONEISEN technique@expert-web.fr
 ********************************************************************************/
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');

global $mod_strings;
global $app_strings;
global $theme;
global $current_language;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $log;

$mode = 'update';

if(isset($_REQUEST['languageid']) && $_REQUEST['languageid']!='')
{
	$mode = 'edit';
	$languageid = $_REQUEST['languageid'];
	$log->debug("the languageid is set to the value ".$languageid);
}
$sql = "select * from vtiger_languages where languageid=".$languageid;
$result = $adb->query($sql);
$languagepackResult = $adb->fetch_array($result);

$smod_strings = return_module_language($current_language,'Settings');

$smarty = new vtigerCRM_smarty;

$smarty->assign("UMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme_path);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MOD", $smod_strings);
$smarty->assign("LANGUAGEID",$_REQUEST['languageid']);
$smarty->assign("LANGUAGE", $languagepackResult["language"]);
$smarty->assign("DATECREATION", $languagepackResult["createddate"]);
$smarty->assign("LASTCHANGE", $languagepackResult["modifiedtime"]);
$smarty->assign("PREFIX", $languagepackResult["prefix"]);
$smarty->assign("ENCODING", $languagepackResult["encoding"]);
$smarty->assign("VERSION", $languagepackResult["version"]);
$smarty->assign("AUTHOR", $languagepackResult["author"]);
$smarty->assign("LICENSE", $languagepackResult["license"]);
$smarty->assign("LOCKFOR", $languagepackResult["lockfor"]);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("PARENTTAB", $_REQUEST['parenttab']);
$smarty->assign("EMODE", $mode);
$smarty->assign("ERROR", $_REQUEST['error']);

$smarty->display("Settings/Languages/LanguagePackCreate.tpl");
?>
