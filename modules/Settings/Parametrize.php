<?php
/*********************************************************************************
** The contents of this file are subject to the Evolutivo BPM License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/


require_once('Smarty_setup.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;
global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;

$sql="select * from vtiger_parametrize";
$result = $adb->pquery($sql, array());
$para_id = $adb->query_result($result,0,'param_id');
$logo_login= decode_html($adb->query_result($result,0,'logo_login'));
$logo_top = decode_html($adb->query_result($result,0,'logo_top'));

if (isset($para_id))
	$smarty->assign("ORGANIZATIONNAME",$para_id);
if (isset($logo_login))
	$smarty->assign("LOGO_LOGIN",$logo_login);
if (isset($logo_top))
	$smarty->assign("LOGO_TOP",$logo_top);


$path = "test/logo";
$dir_handle = @opendir($path) or die("Unable to open directory $path");

while ($file = readdir($dir_handle))
{
   $filetyp =str_replace(".",'',strtolower(substr($file, -4)));
   if($organization_logoname==$file)
   {
        if ($filetyp == 'jpeg' OR $filetyp == 'jpg' OR $filetyp == 'png')
        {
		if($file!="." && $file!="..")
		{

 		     $organization_logopath= $path;
		     $logo_name=$file;
		}

        }
   }
}

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display('Settings/Parametrize.tpl');
?>