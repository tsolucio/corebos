<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Ga�tan KRONEISEN technique@expert-web.fr
 ********************************************************************************/
require_once('modules/Languages/Config.inc.php');
require_once('Smarty_setup.php'); 
require_once('include/database/PearDatabase.php');

global $adb;
global $log;

$log->info("Inside Languages Packs List View");

   $sql = "select * from vtiger_languages order by languageid";
   $result = $adb->query($sql);
   $temprow = $adb->fetch_array($result);
   
require_once('include/utils/UserInfoUtil.php');
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("UMOD", $mod_strings);
global $current_language;
$smod_strings = return_module_language($current_language,'Settings');
$smarty->assign("MOD", $smod_strings);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PARENTTAB", $_REQUEST['parenttab']);
$smarty->assign("ERROR", $_REQUEST['error']);
$smarty->assign("SUCCESS", $_REQUEST['success']);
$smarty->assign("CONFIG_INC_W", (file_exists('config.inc.php') && is_writable('config.inc.php')));

$return_data=array();
if ($temprow != null)
{
do
{
  $templatearray=array();
  $templatearray['language'] = htmlentities($temprow["language"], ENT_QUOTES, $temprow["encoding"]);
  $templatearray['prefix'] = htmlentities($temprow["prefix"], ENT_QUOTES, $temprow["encoding"]);
  $templatearray['languageid'] = htmlentities($temprow["languageid"], ENT_QUOTES, $temprow["encoding"]);
  $templatearray['author'] = htmlentities($temprow["author"], ENT_QUOTES, $temprow["encoding"]);
  $templatearray['createddate'] = htmlentities($temprow["createddate"], ENT_QUOTES, $temprow["encoding"]);
  $templatearray['lockfor']     = isset($ProtectedLanguages[$temprow["prefix"]])?$ProtectedLanguages[$temprow["prefix"]]:htmlentities($temprow["lockfor"], ENT_QUOTES, $temprow["encoding"]);
  $return_data[]=$templatearray;
  $cnt++;
}while($temprow = $adb->fetch_array($result));
}
$smarty->assign("current_language",$current_language);
$smarty->assign("default_language",$default_language);
$log->info("Exiting Languages Packs List View");
$smarty->assign("LANGUAGESPACKEDITOR",$LanguagesPackEditor);
$smarty->assign("LANGUAGESPACKUPLOAD",$LanguagesPackUpload);
$smarty->assign("TEMPLATES",$return_data);
$smarty->display("Settings/Languages/LanguagePacksList.tpl");

?>